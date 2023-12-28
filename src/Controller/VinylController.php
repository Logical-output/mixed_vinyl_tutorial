<?php

namespace App\Controller;


use App\Repository\VinylMixRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;
// use Twig\Environment; Environment $twig

class VinylController extends AbstractController
{
    public function __construct(
        private bool $isDebug
    ){}

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function homepage(): Response
    {
        $tracks = [
            ['song' => 'Gangsta\'s Paradise', 'artist' => 'Coolio'],
            ['song' => 'Waterfalls',          'artist' => 'TLC'],
            ['song' => 'Creep',               'artist' => 'Radiohead'],
            ['song' => 'Kiss from a Rose',    'artist' => 'Seal'],
            ['song' => 'On Bended Knee',      'artist' => 'Boyz II Men'],
            ['song' => 'Fantasy',             'artist' => 'Mariah Carey'],
        ];

        return $this->render('vinyl/homepage.html.twig', [
            'title' => 'PB & Jams',
            'tracks' => $tracks,
        ]);

        /*
        the true way to render a twig template using the twig service
        $html = $twig->render('vinyl/homepage.html.twig', [
            'title' => 'PB & Jams',
            'tracks' => $tracks,
        ]);

        return new Response($html);
        */
    }

    #[Route('/browse/{slug}', name: 'app_browse', methods: ['GET'])]
    public function browse(VinylMixRepository $mixRepository, Request $request, string $slug = null): Response
    {
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;

        $queryBuilder = $mixRepository->createOrderByVotesQueryBuilder($slug);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9,
        );

        return $this->render('vinyl/browse.html.twig', [
            'genre' => $genre,
            'pager' => $pagerfanta,
        ]);
    }
}