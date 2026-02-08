<?php

namespace MauticPlugin\MauticMultiDomainBundle;

use Doctrine\ORM\EntityManager;
use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;

/**
 * Class MauticMultiDomainBundle.
 */
class MauticMultiDomainBundle extends PluginBundleBase
{
    /**
     * Called during plugin installation to create database tables.
     */
    public static function onPluginInstall(Plugin $plugin, ?object $factory = null, $metadata = null, $installedSchema = null): void
    {
        if ($factory !== null && method_exists($factory, 'getEntityManager')) {
            $em = $factory->getEntityManager();
        } else {
            return;
        }

        if (null === $metadata) {
            $metadata = self::getMetadata($em);
        }

        if (null !== $metadata) {
            parent::onPluginInstall($plugin, $factory, $metadata, $installedSchema);
        }
    }

    /**
     * Fix: plugin installer doesn't find metadata entities for the plugin
     * PluginBundle/Controller/PluginController:410.
     *
     * @return array|null
     */
    private static function getMetadata(EntityManager $em): ?array
    {
        $allMetadata   = $em->getMetadataFactory()->getAllMetadata();
        $currentSchema = $em->getConnection()->createSchemaManager()->introspectSchema();

        $classes = [];

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach ($allMetadata as $meta) {
            if (false === strpos($meta->namespace, 'MauticPlugin\\MauticMultiDomainBundle')) {
                continue;
            }

            $table = $meta->getTableName();

            if ($currentSchema->hasTable($table)) {
                continue;
            }

            $classes[] = $meta;
        }

        return $classes ?: null;
    }
}
