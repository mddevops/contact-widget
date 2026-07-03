<?php

namespace SiteApps\ContactWidget\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class RangeSlider extends Field
{
    protected string $view = 'contact-widget::forms.components.range-slider';

    protected int | float $min = 0;

    protected int | float $max = 100;

    protected int | float $step = 1;

    protected string $suffix = 'px';

    public function min(int | float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(int | float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int | float $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function getMin(): int | float
    {
        return $this->min;
    }

    public function getMax(): int | float
    {
        return $this->max;
    }

    public function getStep(): int | float
    {
        return $this->step;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }
}
