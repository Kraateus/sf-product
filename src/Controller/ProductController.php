<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductController extends Controller
{
    /**
     * @Route("/product", name="product_index")
     */
    public function index()
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository(Product::class); //App\Entity\User
        $products = $repository->findAll(); //Tous les utilisateurs

        return $this->render('Products/product.html.twig', [
            'produits' => $products,
        ]);
    }

    /**
     * @Route ("/product/{id}", name="product_show",
     * requirements={"id":"\d+"} )
     */
    public function show($id)
    {
        $product = $this->findProductById($id);

        return $this->render('Products/productdetail.html.twig', [
            'produit' => $product,
        ]);
    }
    /**
     * @Route("/product/create", name="product_create")
     */

    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }
        return $this->render('Products/productcreate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/update", name="product_update")
     */
    public function update(Request $request)
    {

        $id = $request->attributes->get('id');
        $product = $this->findProductById($id);
        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }
        return $this->render('Products/productedit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("product/{id}/delete", name="product_delete")
     */
    public function delete(Request $request)
    {
        $id = $request->attributes->get('id');

        $product = $this->findProductById($id);

        $form = $this
            ->createFormBuilder()
            ->add('confirm', Type\CheckboxType::class, [
                'required' => false,
                'constraints' => [
                    new IsTrue(),
                ],
            ])
            ->add('submit', Type\SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute('product_index'
            );
        }
        return $this->render('Products/productdelete.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function findProductById($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository(Product::class); //App\Entity\User
        $product = $repository->find($id); //Tous les utilisateurs
        if (null === $product) {
            throw $this->createNotFoundException("Produit introuvable");
        }
        return $product;
    }

    private function createProductForm(Product $product)
    {
        return $this
            ->createFormBuilder($product)
            ->add('designation', Type\TextType::class)
            ->add('reference', Type\TextType::class)
            ->add('brand', Type\TextType::class)
            ->add('price', Type\NumberType::class)
            ->add('stock', Type\IntegerType::class)
            ->add('active', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('description', Type\TextareaType::class)
            ->add('submit', Type\SubmitType::class)
            ->getForm();
    }
}
