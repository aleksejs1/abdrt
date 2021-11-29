<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Group;
use App\Entity\Person;
use App\Entity\User;
use App\Form\GroupType;
use App\Form\UserEmailType;
use App\Repository\AccessRepository;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GroupVoter;
use App\Security\Voter\PersonVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/group")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/", name="group_index", methods={"GET"})
     */
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $this->getUser()->getGroups(),
        ]);
    }

    /**
     * @Route("/new", name="group_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$group->getUsers()->contains($this->getUser())) {
                $group->addUser($this->getUser());
            }
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="group_show", methods={"GET"})
     */
    public function show(Group $group): Response
    {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);

        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/addUser", name="group_add_user", methods={"GET", "POST"})
     */
    public function addUser(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);

        $user = new User();
        $form = $this->createForm(UserEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if (!$user) {
                return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
            }
            $group->addUser($user);

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{group}/addPerson/{person}", name="group_add_person", methods={"GET"})
     */
    public function addPerson(
        Group $group,
        Person $person,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);
        $this->denyAccessUnlessGranted(PersonVoter::PERSON, $person);

        $access = new Access($person);
        $access->setAccessGroup($group);

        $entityManager->persist($access);
        $entityManager->flush();

        return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{group}/removePerson/{person}", name="group_remove_person", methods={"GET"})
     */
    public function removePerson(
        Group $group,
        Person $person,
        EntityManagerInterface $entityManager,
        AccessRepository $accessRepository
    ): Response {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);
        $this->denyAccessUnlessGranted(PersonVoter::PERSON, $person);

        $accesses = $accessRepository->findBy(['accessGroup' => $group, 'person' => $person]);
        foreach ($accesses as $access) {
            $entityManager->remove($access);
        }

        $entityManager->flush();

        return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="group_delete", methods={"POST"})
     */
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(GroupVoter::GROUP, $group);

        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
    }
}
