<?php

namespace MauticPlugin\MauticMultiDomainBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MultidomainController.
 */
class MultidomainController extends AbstractStandardFormController
{
    /**
     * @return string
     */
    protected function getModelName(): string
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    protected function getJsLoadMethodPrefix(): string
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    protected function getRouteBase(): string
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    protected function getSessionBase($objectId = null): string
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    protected function getTemplateBase(): string
    {
        return '@MauticMultiDomain/Multidomain';
    }

    /**
     * @return string
     */
    protected function getTranslationBase(): string
    {
        return 'mautic.multidomain';
    }

    /**
     * @return class-string
     */
    protected function getEntityClass(): string
    {
        return Multidomain::class;
    }

    /**
     * @return string
     */
    protected function getDefaultOrderColumn()
    {
        return 'email';
    }

    /**
     * @return JsonResponse|RedirectResponse|Response
     */
    public function indexAction(Request $request, int $page = 1)
    {
        return parent::indexStandard($request, $page);
    }

    /**
     * Generates new form and processes post data.
     *
     * @return JsonResponse|Response
     */
    public function newAction(Request $request)
    {
        return parent::newStandard($request);
    }

    /**
     * Generates edit form and processes post data.
     *
     * @return JsonResponse|Response
     */
    public function editAction(Request $request, int $objectId, bool $ignorePost = false)
    {
        return parent::editStandard($request, $objectId, $ignorePost);
    }

    /**
     * Displays details on a multidomain.
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function viewAction(Request $request, int $objectId)
    {
        return parent::viewStandard($request, $objectId, 'multidomain', 'plugin.multidomain');
    }

    /**
     * Clone an entity.
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function cloneAction(Request $request, int $objectId)
    {
        return parent::cloneStandard($request, $objectId);
    }

    /**
     * Deletes the entity.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction(Request $request, int $objectId)
    {
        return parent::deleteStandard($request, $objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function batchDeleteAction(Request $request)
    {
        return parent::batchDeleteStandard($request);
    }
}
