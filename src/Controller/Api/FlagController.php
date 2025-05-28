<?php

namespace Tourze\CurrencyManageBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 国旗图片控制器
 */
class FlagController extends AbstractController
{
    public function __construct(
        private readonly FlagService $flagService,
    )
    {
    }

    #[Route('/currency/flag/{code}', name: 'currency_flag', methods: ['GET'])]
    public function flag(string $code): Response
    {
        $flagFile = $this->flagService->getFlagPath($code, '4x3');

        if (!$flagFile) {
            throw new NotFoundHttpException('Flag not found for code: ' . $code);
        }

        // 返回文件响应
        $response = new BinaryFileResponse($flagFile);
        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->headers->set('Cache-Control', 'public, max-age=86400'); // 缓存1天

        return $response;
    }

    #[Route('/currency/flag/{code}/1x1', name: 'currency_flag_1x1', methods: ['GET'])]
    public function flag1x1(string $code): Response
    {
        $flagFile = $this->flagService->getFlagPath($code, '1x1');

        if (!$flagFile) {
            throw new NotFoundHttpException('Flag not found for code: ' . $code);
        }

        // 返回文件响应
        $response = new BinaryFileResponse($flagFile);
        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->headers->set('Cache-Control', 'public, max-age=86400'); // 缓存1天

        return $response;
    }
}
