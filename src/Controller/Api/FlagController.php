<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 国旗图片控制器 (4x3 比例)
 */
final class FlagController extends AbstractController
{
    public function __construct(
        private readonly FlagService $flagService,
    ) {
    }

    #[Route(path: '/currency/flag/{code}', name: 'currency_flag', methods: ['GET'])]
    public function __invoke(string $code): Response
    {
        $flagFile = $this->flagService->getFlagPath($code, '4x3');

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
