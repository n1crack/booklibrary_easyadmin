<?php

namespace App\Subscribers;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class BookSubscriber implements EventSubscriberInterface
{
  private $params;

  private $filesystem;

  public function __construct(ContainerBagInterface $params, Filesystem $filesystem)
  {
    $this->params = $params;
    $this->filesystem = $filesystem;
  }

  public static function getSubscribedEvents()
  {
    return [
      AfterEntityDeletedEvent::class => ['deleteFile']
    ];
  }

  public function deleteFile(AfterEntityDeletedEvent $event)
  {
    $entity = $event->getEntityInstance();

    if ($entity instanceof Book) {
      $this->filesystem->remove([$this->params->get('upload_directory') . '/' . $entity->getImage()]);
    }
  }
}
