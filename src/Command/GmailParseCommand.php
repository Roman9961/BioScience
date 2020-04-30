<?php

namespace App\Command;


use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\ShippingAddress;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class GmailParseCommand extends Command
{
    /**
     * @var Google_Client
     */
    private $googleClient;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var ArrayCollection
     */
    private $products;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * GoogleClient constructor.
     */
    public function __construct(Google_Client $googleClient, EntityManagerInterface $em, LoggerInterface $logger, KernelInterface $appKernel)
    {
        $this->googleClient = $googleClient;
        $this->em = $em;
        $this->products = $this->em->getRepository('App\Entity\Product')->findAll();
        parent::__construct();

        $this->logger = $logger;
        $this->appKernel = $appKernel;
    }


    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('gmail:parse')
            ->addOption('authCode', 'ac', InputOption::VALUE_OPTIONAL, 'authCode');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->isProcessRunning();
        $this->initProcess();
//        $this->googleClient->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        try {
            $this->googleClient->setScopes(Google_Service_Gmail::GMAIL_READONLY);
            $this->googleClient->setAccessType('offline');
            $tokenPath = $this->appKernel->getProjectDir().'/token.json';
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $this->googleClient->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($this->googleClient->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($this->googleClient->getRefreshToken()) {
                    $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $this->googleClient->createAuthUrl();
                    $output->writeln("Open the following link in your browser and get verification code:\n{$authUrl}");


                    $helper = $this->getHelper('question');
                    $question = new Question('Enter verification code: ', '');

                    $authCode = $helper->ask($input, $output, $question);

                    // Exchange authorization code for an access token.
                    $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($authCode);
                    $this->googleClient->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($this->googleClient->getAccessToken()));
            }
            $service = new Google_Service_Gmail($this->googleClient);

            $this->nextMessages($service);
            $this->stopProcess();
        }catch (\Exception $e){
            $this->stopProcess();
        }
    }

    private function isParsedMessage($messageId)
    {
        return !!$this->em->getRepository('App\Entity\Customer')->findOneBy(['messageId'=> $messageId]);
    }

    private function getMessageList(Google_Service_Gmail $service)
    {

    }

    private function nextMessages(Google_Service_Gmail $service, $nextPage = null)
    {
        $user = 'me';
        $optParams = ['maxResults'=>4, 'includeSpamTrash'=>true, 'q'=>'has shipped'];
        if($nextPage){
            $optParams['pageToken'] = $nextPage;
        }
        $userMessages = $service->users_messages;
        $messageList = $userMessages->listUsersMessages($user, $optParams);


        $nextPage = $messageList->getNextPageToken();

        if($nextPage){
//            $this->setLastPage($nextPage);
        }

        $isParsed = true;

        if(count($messageList->getMessages())>0) {
            $orderInf =[];
            try {
                foreach ($messageList->getMessages() as $message) {

                    /**
                     * @var Google_Service_Gmail_Message $gmessage
                     */
                    $gmessage = $message;

                    $messageId = $message->getId();
                    preg_match('/ Sent: (.*) To:/ms', $userMessages->get($user, $messageId)->getSnippet(), $orderDate);

                    $isParsed = $this->isParsedMessage($messageId);
                    $messagePayload =  $userMessages->get($user, $messageId)->getPayload();
                    $rawData = $this->getRawData($messagePayload);

                    $orderInf = $this->getOrderInfo($rawData);

                    if(empty($orderInf['order_id'])){
                       continue;
                    }

                    $orderInf['messageId'] = $messageId;
                    $orderInf['order_date'] = new \DateTime($orderDate[1]);

                    echo $orderInf['order_id'].'|||';
                    echo strval($isParsed).PHP_EOL;

                    if(!$isParsed && $rawData) {
                        $this->addCustomerOrder($orderInf);
                    }
                }
                while ($nextPage && !$isParsed) {
                    $this->nextMessages($service, $nextPage);

                    break;
                }
                $this->em->flush();

            }catch (\Exception $e){
                $this->logger->error('Error', ['error'=> $e->getLine(), 'order' => $orderInf]);
                $this->stopProcess();
            }
        }

    }

    private function addCustomerOrder($orderInfo)
    {
        $customer = new Customer();
        $customer->setOrderId($orderInfo['order_id']);
        $customer->setShippingInfo($orderInfo['shippingAddress']);
        $customer->setOrderDate($orderInfo['order_date']);
        $customer->setMessageId($orderInfo['messageId']);
        foreach ($orderInfo['products'] as $product){
            $customer->addProduct($product);
        }
        $this->em->persist($customer);
    }

    private function getRawData($payload){
        $rawData = $payload->getBody()->getData();
        if(!$rawData){
            foreach ($payload->getParts() as $part){
                $rawData = $this->getRawData($part);
                if($rawData){
                    break;
                }
            }
        }
        return $rawData;
    }

    private function getOrderInfo($body) {
        $rawData = $body;
        $sanitizedData = strtr($rawData,'-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
        if(!$decodedMessage){
            $decodedMessage = false;
        }

        preg_match('/Order \((.*)\).*customers:.*\\r?\\n(.*\d? [A-Z]{2})/m', $decodedMessage, $res);
        $orderInfo = [];
        if(count($res) === 3){
            $orderInfo['order_id'] = $res[1];
            $shippingRawData = $res;
            $products = [];
            foreach ($this->products as $product){
                preg_match('/'.$product->getName().'/ms', $decodedMessage, $res);
                if(count($res)>0){
                    $products[] = $product;
                }
            }

            $shippingAddress = $this->createShippingAddress($shippingRawData);

            $orderInfo['shippingAddress'] = $shippingAddress;
            $orderInfo['products'] =  $products;
        }
        return $orderInfo;
    }

    private function isProcessRunning()
    {
        if (file_exists($this->appKernel->getProjectDir().'/pid')){
            $this->logger->warning('Process running', []);
            throw new \RuntimeException('Process running');
        }
    }

    private function initProcess()
    {
        try {
            file_put_contents($this->appKernel->getProjectDir().'/pid', uniqid());
        }catch (\Exception $e){
            $this->logger->error('Init process error', ['error'=>$e->getMessage()]);
        }
    }

    private function stopProcess()
    {
        try {
            unlink($this->appKernel->getProjectDir().'/pid');
        }catch (\Exception $e){
            $this->logger->error('Stop process error', ['error'=>$e->getMessage()]);
        }
    }

    private function createShippingAddress($shippingRawData)
    {
        $city = explode(',',$shippingRawData[2]);
        $shipInfo = count($city)>1 ? $city[1] :$city[0];
        preg_match('/(.*\\s?.*)\\s(.*)\\s([A-Z]{2})/ms', $shipInfo, $res);

        $shippingAddress = new ShippingAddress();

        $shippingAddress->city =  count($city)>1 ? rtrim($city[0]) : $res[1];
        $shippingAddress->state =  $res[1];
        $shippingAddress->zipCode =  $res[2];
        $shippingAddress->country =  $res[3];

        return $shippingAddress;
    }
}
