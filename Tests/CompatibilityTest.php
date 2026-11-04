<?php

namespace NS\UtilBundle\Tests;

use NS\UtilBundle\Form\Types\ClockPicker;
use NS\UtilBundle\Form\Types\DateClockPicker;
use NS\UtilBundle\Form\Types\DatePickerType;
use NS\UtilBundle\Form\Types\DateTimePicker;
use NS\UtilBundle\Form\Types\TimeType;
use NS\UtilBundle\Service\Ajax;
use NS\UtilBundle\Tests\Fixtures\CompatibilityKernel;
use NS\UtilBundle\Tests\Form\Fixtures\SubArrayChoice;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormRenderer;

class CompatibilityTest extends TestCase
{
    public function testSourceClassesLoad(): void
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__.'/../src')) as $file) {
            if ($file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
        self::assertTrue(class_exists(\NS\UtilBundle\Form\Transformers\SetChoiceTransformer::class));
        self::assertTrue(class_exists(\NS\UtilBundle\Form\Transformers\CollectionToAjaxJson::class));
    }

    public function testBundleServicesAndFormTheme(): void
    {
        $kernel = new CompatibilityKernel('test', false);
        try {
            $kernel->boot();
            $container = $kernel->getContainer();
            self::assertInstanceOf(Ajax::class, $container->get('test.ajax'));
            $factory = $container->get('test.form_factory');
            $renderer = $container->get('test.twig')->getRuntime(FormRenderer::class);

            foreach ([TimeType::class, ClockPicker::class, DatePickerType::class, DateTimePicker::class, DateClockPicker::class, SubArrayChoice::class] as $type) {
                $form = $factory->create($type);
                $html = $renderer->searchAndRenderBlock($form->createView(), 'widget');
                self::assertNotEmpty($html);
                if ($type === DatePickerType::class) {
                    self::assertStringContainsString('new Pikaday', $html);
                }
            }

            $time = $factory->create(TimeType::class, null, [
                'input' => 'datetime_immutable',
                'preferred_choices' => ['hours' => [13], 'minutes' => [5]],
            ]);
            $time->submit(['hour' => '13', 'minute' => '5']);
            self::assertTrue($time->isSynchronized());
            self::assertInstanceOf(\DateTimeImmutable::class, $time->getData());
            self::assertSame('13:05', $time->getData()->format('H:i'));
        } finally {
            $kernel->shutdown();
        }
    }
}
