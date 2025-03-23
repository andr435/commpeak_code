<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\CallReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class MainController extends AbstractController
{
    #[Route('/call/api', name: 'app_main', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entity_manager): JsonResponse
    {
        $last_update_get = $request->query->get('last_update');
        $cache = new FilesystemAdapter();
        $last_update = $cache->getItem('calls.last_update');
        if ($last_update_get == $last_update->get()) {
            return $this->json([
                'success' => true,
                'message' => 'No new data',
                'last_update' => $last_update->get(),
            ]);
        }

        $data = $entity_manager->getRepository(CallReport::class)
            ->getStatistics();
        return $this->json([
            'success' => true,
                'data' => $data,
                'last_update' => $last_update->get()
        ]);
    }

    #[Route('/call/api', name: 'post_main', methods: ['POST'])]
    public function new_calls(Request $request, EntityManagerInterface $entity_manager): JsonResponse
    {
        $file = $request->files->get('uploadcsv');

        if ($file) {
            try {
                $call_array = [];
                // Check if the file is a CSV and have correct numbers of fields
                $raws = explode("\n", trim(file_get_contents($file->getPathname())));
                foreach ($raws as $row){
                    $call_data = str_getcsv($row);
                    if (count($call_data) !== 5) {
                       throw new \Exception('Invalid CSV file');
                    }
                    $call_array[] = $call_data;
                }

                # Save the data to the database
                $cnt = 0;
                foreach ($call_array as $call) {
                    $call_report = new CallReport($entity_manager);
                    $call_report->setCustomerId($call[0]);
                    $call_report->setCallDate(new \DateTime($call[1]));
                    $call_report->setDuration($call[2]);
                    $call_report->setDialedNumber($call[3]);
                    $call_report->setCustomerIp($call[4]);
                    $entity_manager->persist($call_report);
                    $cnt++;
                    if ($cnt % 300 == 0) {
                        $entity_manager->flush();
                        $entity_manager->clear();
                    }
                }
                $entity_manager->flush();
                $cache = new FilesystemAdapter();
                $last_update = $cache->getItem('calls.last_update');
                $last_update->set(time());
                $cache->save($last_update);
                
                return $this->json([
                    'success' => true,
                    'last_update' => $last_update->get(),
                ]);
            } catch (\Exception $e) {
                return $this->json([
                    'error' => "Invalid CSV file, {$e->getMessage()}",
                ]);
            }
        }
        return $this->json([
            'error' => 'No file uploaded',
        ]);
    }

    #[Route('/', name: 'main_page', methods: ['GET'])]
    public function main(): Response
    {
        return $this->render('main/index.html.twig');
    }
}
