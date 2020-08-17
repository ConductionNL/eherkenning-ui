<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeveloperController.
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
        $token = $request->query->get('token');
        $responceUrl = $request->query->get('responceUrl');
        $backUrl = $request->query->get('backUrl');
        $brpUrl = $request->query->get('brpUrl');
        $url = $request->getHost();
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.kvk.nl',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', '/api/v2/testsearch/companies?q=test&mainBranch=true&branch=false');
        $kvk = json_decode($response->getBody()->getContents(), true);

        if ($brpUrl) {
            $people = $commonGroundService->getResourceList($brpUrl);
        } else {
            $people = $commonGroundService->getResourceList(['component'=>'brp', 'type'=>'ingeschrevenpersonen'])['hydra:member'];
        }

        return ['people'=>$people, 'kvk'=>$kvk, 'responceUrl' => $responceUrl, 'backUrl' => $backUrl, 'token' => $token];
    }
}
