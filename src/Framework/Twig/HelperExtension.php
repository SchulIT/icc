<?php

namespace App\Framework\Twig;

use App\Framework\Utils\ColorUtils;
use App\Framework\View\Filter\FilterViewInterface;
use DateInterval;
use DateTime;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension {

    public function __construct(
        private RefererHelper $redirectHelper,
        private ColorUtils $colorUtils,
        private ValidatorInterface $validator
    ) { }

    public function getFilters(): array {
        return [
            new TwigFilter('previous_date', [ $this, 'getPreviousDate' ]),
            new TwigFilter('next_date', [ $this, 'getNextDate']),
            new TwigFilter('clone', [ $this, 'cloneObject' ])
        ];
    }

    public function getFunctions(): array {
        return [

            new TwigFunction('referer_path', [ $this, 'refererPath' ]),
            new TwigFunction('foreground', [ $this, 'foregroundColor' ]),
            new TwigFunction('validation_errors', [ $this, 'validate' ]),
            new TwigFunction('contains_active_filters', [ $this, 'containsActiveFilters']),
            new TwigFunction('is_in_datetime_array', [ $this, 'isInDateTimeArray']),
            new TwigFunction('percent', [ $this, 'percent' ]),
        ];
    }

    public function getPreviousDate(DateTime $dateTime, bool $skipWeekends = false): DateTime {
        $previous = (clone $dateTime)->sub(new DateInterval('P1D'));

        while($skipWeekends === true && $previous->format('N') > 5) {
            $previous->modify('-1 day');
        }

        return $previous;
    }

    public function getNextDate(DateTime $dateTime, bool $skipWeekends = false): DateTime {
        $next = (clone $dateTime)->add(new DateInterval('P1D'));

        while($skipWeekends === true && $next->format('N') > 5) {
            $next->modify('+1 day');
        }

        return $next;
    }

    public function cloneObject(object $object): object {
        return clone $object;
    }

    public function isInDateTimeArray(DateTime $dateTime, array $dateTimes): bool {
        return array_any($dateTimes, fn($item) => $item == $dateTime);

    }

    public function refererPath(array $mapping, string $route, array $parameters = [ ]): string {
        return $this->redirectHelper->getRefererPathFromQuery($mapping, $route, $parameters);
    }

    public function foregroundColor(string $color): string {
        return $this->colorUtils->getForeground($color);
    }

    public function validate($object): ConstraintViolationListInterface {
        return $this->validator->validate($object);
    }

    /**
     * @param FilterViewInterface[] $filters
     */
    public function containsActiveFilters(array $filters): bool {
        return array_any($filters, fn($filter) => $filter->isEnabled());

    }

    public function percent(float $value, float $total): float {
        if($total === 0.0) {
            return 0;
        }

        return $value / $total * 100;
    }
}