<?php 
namespace App\Controller;
use App\Entity\Dish;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\DishType;
use App\Form\Type\DishEdit;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
class DishController extends AbstractController
{
  public $query ='';
  public function __construct(private ManagerRegistry $doctrine) {
  }
      #[Route("/Dish", name:"Dish_view")]
     
    public function dish_view(EntityManagerInterface $em,DishRepository $dishRepository,Request $request):Response
    {
         $page =$request->query->get('page');
         $perPage = 8;
         $start =($page -1) * $perPage;
         $end =$page * $perPage;
         $dish = new dish();
         $value = $dish->getId();
         $dishData =$dishRepository->findAllData($page,$perPage,$start, $end,$value,$request);
         $totalPage = round(count($dishData) / $perPage);
            return $this->render('dish/dish_view.html.twig', [
           'title'=>'Dish View',
           'data'=>$dishData,
           'page'=>$page,
           'totalPage' =>$totalPage,
            ]);
    }
    public function tablelist(EntityManagerInterface $em,DishRepository $dishRepository,Request $request):Response
    {
        $page =$request->query->get('page');
        $perPage = 8;
        $start =($page -1) * $perPage;
        $end =$page * $perPage;
        $dish = new dish();
        $value = $dish->getId();
        $inc = 1;
        $dishData =$dishRepository->findAllData($page,$perPage,$start, $end,$value,$request);
        $totalPage = round(count($dishData) / $perPage);
        return $this->render('dish/tablelist.html.twig',[
            'title'=>'Dish Table',
            'data'=>$dishData,
            'page'=>$page,
            'totalPage' =>$totalPage,
            'inc'=>$inc,
        ]);
    }
    #[Route("deletelist/{id}",methods:['GET','DELETE'])]
    public function deletelist(Int $id,ManagerRegistry $doctrine,DishRepository $dishRepository,Request $request,EntityManagerInterface $em):Response
    {
        $dishData = $doctrine->getRepository(Dish::class)->find($id);
        $em->remove($dishData);
        $em->flush();
       return $this->redirectToRoute('tablelist');
    }
   
    public function create_dish(Request $request): Response
    {
        $dish = new Dish();
        $entityManager = $this->doctrine->getManager();
       // creates a task object and initializes some data for this example
       $form = $this->createForm(DishType::class, $dish,[
            'action' => $this->generateUrl('post_dish'),
            'method' => 'POST',
       ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $dish = $form->getData();

            // ... perform some action, such as saving the task to the database
            $file = $dish->getImage(); 
            $fileName = md5(uniqid()).'.'.$file->guessExtension(); 
            $file->move($this->getParameter('kernel.uploads'), $fileName); 
            $dish->setImage($fileName);
            $dish->setCreatedAt(new \DateTimeImmutable());
            $dish = $form->getData();
            $entityManager->persist($dish);
            $entityManager->flush();
            return $this->redirectToRoute('create_dish');
        }
       return $this->renderForm('dish/create_dish.html.twig',[
                 'form'=>$form,
            ]);
    }
    #[Route("dish/edit_dish/{id}",methods:['GET','UPDATE'])]
    public function edit_dish(Int $id,ManagerRegistry $doctrine,Request $request):Response
    {
        $dish = new Dish();
        $dish= $doctrine->getRepository(Dish::class)->find($id);
       // creates a task object and initializes some data for this example
       $form = $this->createForm(DishEdit::class, $dish,[
            // 'action' => $this->generateUrl('post_edit'),
            // 'method' => 'POST',
            'data' =>$dish,
       ]);
       $dishData = $doctrine->getRepository(Dish::class)->find($id);
       return $this->renderForm('dish/edit_dish.html.twig',[
           'title'=>'Dish Edit Form',
           'data'=>$dish,
           'form'=>$form
       ]);
    }
    public function post_edit(Int $id,Request $request,ManagerRegistry $doctrine){
        $dish = new Dish();
        $dish = $doctrine->getRepository(Dish::class)->find($id);
        $entityManager = $this->doctrine->getManager();
       // creates a task object and initializes some data for this example
       $form = $this->createForm(DishEdit::class, $dish,[
            // 'action' => $this->generateUrl('post_edit'),
            // 'method' => 'POST',
            'data' =>$dish,
       ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $dish = $form->getData();

            // ... perform some action, such as saving the task to the database
            $file = $dish->getImage(); 
            // dd($file);
            $fileName = md5(uniqid()).'.'.$file->guessExtension(); 
            $file->move($this->getParameter('kernel.uploads'), $fileName); 
            $dish->setImage($fileName);
            $dish->setCreatedAt(new \DateTimeImmutable());
            $dish = $form->getData();
            $entityManager->persist($dish);
            $entityManager->flush();
            return $this->redirectToRoute('tablelist');
        }
    }
}