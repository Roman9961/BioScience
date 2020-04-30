<?php


namespace App\Controller;


use App\Entity\Customer;
use App\Entity\ShippingAddress;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController  extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * DefaultController constructor.
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, SerializerInterface $serializer, \Swift_Mailer $mailer)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->mailer = $mailer;
    }


    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('Default/index.html.twig');
    }

    /**
     * @Route("/step1")
     */
    public function step1(Request $request)
    {
        $data = $request->request->all();

        /**
         * @var Customer $customer
         */
        $customer = $this->em->getRepository('App\Entity\Customer')->findOneBy(['orderId'=> $data['order_id']]);
        $product = $this->em->getRepository('App\Entity\Product')->findOneBy(['id'=> $data['product']['id']]);

        $hasByEmail = $this->em->getRepository('App\Entity\Customer')->hasByEmail($data['email']);
        $hasByOrderId = $this->em->getRepository('App\Entity\Customer')->hasByOrderId($data['order_id']);


        if(!$customer){
            return new JsonResponse(['message'=> 'order not found', 'field' => 'order_id'], 404);
        }elseif (!$customer->hasProduct($product)){
            return new JsonResponse(['message'=> 'Wrong product', 'field' => 'general'], 404);
        }
        elseif ($customer->getIsRequested() || $hasByEmail || $hasByOrderId){
            return new JsonResponse(['message'=> 'we already send you a gift', 'field' => 'general'], 404);
        }
        try {
            $shippingInfo = $customer->getShippingInfo();
            $shippingInfo->setName($data['first_name'].' '.$data['last_name']);
            $customer->setEmail($data['email']);
            $customer->setFirstName($data['first_name']);
            $customer->setLastName($data['last_name']);
            $this->em->persist($customer);
            $this->em->flush();
        }catch (\Exception $e){
            return new JsonResponse(['message'=> 'Something went wrong, try again later', 'field' => 'general'], 404);
        }


        return new JsonResponse($this->serializer->serialize($customer, 'json'));
    }

    /**
     * @Route("/step2")
     */
    public function step2(Request $request)
    {
        $data = $request->request->all();

        /**
         * @var Customer $customer
         */
        $customer = $this->em->getRepository('App\Entity\Customer')->findOneBy(['orderId'=> $data['order_id']]);
        $gradeLimit = $this->em->getRepository('App\Entity\GradeLimit')->find(1);

        if(!$customer){
            return new JsonResponse(['message'=> 'order not found', 'field' => 'general'], 404);
        }
        try {
            $customer->setGrade($data['grad']);
            $customer->setFeedback($data['feedback']);
            $this->em->persist($customer);
            $this->em->flush();
        }catch (\Exception $e){
            return new JsonResponse(['message'=> 'Server error', 'field' => 'general'], 404);
        }


        return new JsonResponse($this->serializer->serialize(['customer' => $customer, 'isShowAmazon' => $customer->getGrade() >= $gradeLimit->getLimit() ], 'json'));
    }


    /**
     * @Route("/step4")
     */
    public function step4(Request $request)
    {
        $data = $request->request->all();

        /**
         * @var Customer $customer
         */

        $repo =  $this->em->getRepository('App\Entity\Customer');
        $customer = $repo->findOneBy(['orderId'=> $data['order_id']]);

        if(!$customer){
            return new JsonResponse(['message'=> 'order not found', 'field' => 'general'], 404);
        }

        $shippingAddress = ShippingAddress::fromArray($data['shippingInfo']);

        if($repo->hasByAddress($shippingAddress)){

            return new JsonResponse(['message'=> 'we already send you a gift', 'field' => 'general'], 404);
        }

        try {
            $customer->setIsRequested(true);
            $customer->setShippingInfo($shippingAddress);
            $customer->setFeedbackDate(new \DateTime());
            $this->em->persist($customer);
            $this->em->flush();
        }catch (\Exception $e){
            return new JsonResponse(['message'=> 'Server error', 'field' => 'general'], 404);
        }

        try {
            $body = $customer->getFirstName().' '. $customer->getLastName().' make order.'.PHP_EOL;
            $body .= 'Go to '.$request->getHost().'/admin for additional information.';
            $mess = new \Swift_Message('New order #'.$customer->getOrderId() , $body);
            $mess->addTo('support@nugema.com');
            $mess->addFrom('naturalbioscience.promo@gmail.com');

            $this->mailer->send($mess);
        } catch (\Exception $e) {
            $this->logger->debug('Send mail error', ['error' => $e->getMessage()]);
        }


        return new JsonResponse('ok');
    }

    /**
     * @Route("/products")
     */
    public function products()
    {
        $products = $this->em->getRepository('App\Entity\Product')->findAll();

        return new JsonResponse($this->serializer->serialize($products, 'json'));
    }

}