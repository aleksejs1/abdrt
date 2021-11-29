<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Person;
use App\Form\PersonType;
use App\Security\Voter\PersonVoter;
use App\Service\PersonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/person")
 */
class PersonController extends AbstractController
{
    /**
     * @Route("/", name="person_index", methods={"GET"})
     */
    public function index(PersonService $personService): Response
    {
        return $this->render('person/index.html.twig', [
            'people' => $personService->getBirthdaySortedList($this->getUser()),
        ]);
    }

    /**
     * @Route("/new", name="person_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $access = new Access($person, $this->getUser());
            $entityManager->persist($access);
            $entityManager->flush();

            return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('person/new.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="person_show", methods={"GET"})
     */
    public function show(Person $person): Response
    {
        $this->denyAccessUnlessGranted(PersonVoter::PERSON, $person);

        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="person_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(PersonVoter::PERSON, $person);

        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('person/edit.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="person_delete", methods={"POST"})
     */
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(PersonVoter::PERSON, $person);

        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $entityManager->remove($person);
            $entityManager->flush();
        }

        return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
    }
}
