<?php

namespace App\Controller;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DefaultController constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $gradeLimit = $this->em->getRepository('App\Entity\GradeLimit')->find(1);
        return $this->render('admin/index.html.twig', ['gradeLimit' => $gradeLimit->getLimit()]);
    }

    /**
     * @Route("/admin/data", name="admin_data")
     */
    public function data(Request $request)
    {
        $parameters = $request->query->all();

        $orders = $this->em->getRepository('App\Entity\Customer')->findOrders($parameters);

        return $this->json($orders);
    }

    /**
     * @Route("/admin/is_send", name="admin_is_send")
     */
    public function isSend(Request $request)
    {
        $customer = $this->em->getRepository(Customer::class)->find($request->get('customer'));
        $customer->setIsSended(!$customer->getIsSended());
        $this->em->flush();
        return $this->json([]);
    }

    /**
     * @Route("/admin/set_grade_limit", name="admin_set_grade_limit")
     */
    public function setGradeLimit(Request $request)
    {
        $gradeLimit = $this->em->getRepository('App\Entity\GradeLimit')->find(1);
        $gradeLimit->setLimit(intval($request->get('gradeLimit')));
        $this->em->flush();
        return $this->json(['gradeLimit' => $gradeLimit->getLimit()]);
    }
}
