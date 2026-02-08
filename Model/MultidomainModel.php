<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticMultiDomainBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;
use MauticPlugin\MauticMultiDomainBundle\Event\MultidomainEvent;
use MauticPlugin\MauticMultiDomainBundle\Form\Type\MultidomainType;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MultidomainModel extends FormModel
{
    /**
     * @var \Mautic\FormBundle\Model\FormModel
     */
    protected $formModel;

    /**
     * @var TrackableModel
     */
    protected $trackableModel;

    /**
     * @var FieldModel
     */
    protected $leadFieldModel;

    /**
     * @var ContactTracker
     */
    protected $contactTracker;

    public function __construct(
        EntityManagerInterface $em,
        CorePermissions $security,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Translator $translator,
        UserHelper $userHelper,
        LoggerInterface $logger,
        CoreParametersHelper $coreParametersHelper,
        \Mautic\FormBundle\Model\FormModel $formModel,
        TrackableModel $trackableModel,
        FieldModel $leadFieldModel,
        ContactTracker $contactTracker,
    ) {
        parent::__construct($em, $security, $dispatcher, $router, $translator, $userHelper, $logger, $coreParametersHelper);
        $this->formModel      = $formModel;
        $this->trackableModel = $trackableModel;
        $this->leadFieldModel = $leadFieldModel;
        $this->contactTracker = $contactTracker;
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'multidomain:items';
    }

    /**
     * {@inheritdoc}
     */
    public function createForm($entity, FormFactoryInterface $formFactory, $action = null, $options = []): FormInterface
    {
        if (!$entity instanceof Multidomain) {
            throw new MethodNotAllowedHttpException(['Multidomain']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(MultidomainType::class, $entity, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticMultiDomainBundle\Entity\MultidomainRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Multidomain::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($id = null): ?object
    {
        if (null === $id) {
            return new Multidomain();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param Multidomain      $entity
     * @param bool|false $unlock
     */
    public function saveEntity($entity, $unlock = true): void
    {
        parent::saveEntity($entity, $unlock);
        $this->getRepository()->saveEntity($entity);
    }

    public function generateMessageId(Multidomain $multidomain) {
        $url = $multidomain->getDomain();
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            throw new \Exception("InvalidDomainError");
        }

        $messageIdSuffix = '@' . $parts['host'];
        return bin2hex(random_bytes(16)).$messageIdSuffix;
    }

    /**
     * Get whether the color is light or dark.
     *
     * @param $hex
     * @param $level
     *
     * @return bool
     */
    public static function isLightColor($hex, $level = 200)
    {
        $hex = str_replace('#', '', $hex);
        $r   = hexdec(substr($hex, 0, 2));
        $g   = hexdec(substr($hex, 2, 2));
        $b   = hexdec(substr($hex, 4, 2));

        $compareWith = ((($r * 299) + ($g * 587) + ($b * 114)) / 1000);

        return $compareWith >= $level;
    }

    /**
     * {@inheritdoc}
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, ?Event $event = null): ?Event
    {
        if (!$entity instanceof Multidomain) {
            throw new MethodNotAllowedHttpException(['Multidomain']);
        }

        switch ($action) {
            case 'pre_save':
                $name = 'mautic.multidomain_pre_save';
                break;
            case 'post_save':
                $name = 'mautic.multidomain_post_save';
                break;
            case 'pre_delete':
                $name = 'mautic.multidomain_pre_delete';
                break;
            case 'post_delete':
                $name = 'mautic.multidomain_post_delete';
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new MultidomainEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($event, $name);

            return $event;
        }

        return null;
    }

    // Get path of the config.php file.
    public function getConfiArray()
    {
        return include dirname(__DIR__).'/Config/config.php';
    }
}
