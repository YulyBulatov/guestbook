<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return new Response($this->twig->render('conference/index.html.twig', array('conferences' => $conferenceRepository->findAll())));
    }

    /**
     * @Route("/conference/{id}", name="conference")
     */
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository)
    {
        $offset = max(0,$request->query->getInt('offset',0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);
    return new Response($this->twig->render('conference/show.html.twig', [
        'conference' => $conference,
        'comments' => $paginator,
        'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
        'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE)  
    ]));
    }   
}
