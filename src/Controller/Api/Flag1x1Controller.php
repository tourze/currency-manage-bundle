<?php

namespace Tourze\CurrencyManageBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 国旗图片控制器 (1x1 比例)
 */
class Flag1x1Controller extends AbstractController
{
    public function __construct(
        private readonly FlagService $flagService,
    )
    {
    }

    #[Route(path: '/currency/flag/{code}/1x1', name: 'currency_flag_1x1', methods: ['GET'])]
    public function __invoke(string $code): Response
    {
        $flagFile = $this->flagService->getFlagPath($code, '1x1');

        if (null === $flagFile) {
            throw new NotFoundHttpException('Flag not found for code: ' . $code);
        }

        // 返回文件响应
        $response = new BinaryFileResponse($flagFile);
        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->headers->set('Cache-Control', 'public, max-age=86400'); // 缓存1天

        return $response;
    }
}