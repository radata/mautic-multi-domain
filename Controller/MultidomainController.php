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
     * @param int $page
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {
        return parent::indexStandard($page);
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
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|Response
     */
    public function editAction($objectId, $ignorePost = false)
    {
        return parent::editStandard($objectId, $ignorePost);
    }

    /**
     * Displays details on a multidomain.
     *
     * @param $objectId
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        return parent::viewStandard($objectId, 'multidomain', 'plugin.multidomain');
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        return parent::cloneStandard($objectId);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction($objectId)
    {
        return parent::deleteStandard($objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function batchDeleteAction()
    {
        return parent::batchDeleteStandard();
    }
}
