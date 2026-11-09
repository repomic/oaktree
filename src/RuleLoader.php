<?php

namespace Oaktree;

use Oaktree\Components\ButtonComponet;
use Oaktree\Components\DialogComponent;
use Oaktree\Components\MessageComponent;
use Oaktree\Rules\AnchorRule;
use Oaktree\Rules\BackgroundRule;
use Oaktree\Rules\BorderRule;
use Oaktree\Rules\ContainerRule;
use Oaktree\Rules\EffectRule;
use Oaktree\Rules\FiltersRule;
use Oaktree\Rules\FlexboxGridRule;
use Oaktree\Rules\FormRule;
use Oaktree\Rules\InteractivityRule;
use Oaktree\Rules\LayoutRule;
use Oaktree\Rules\ListRule;
use Oaktree\Rules\RuleInterface;
use Oaktree\Rules\SizingRule;
use Oaktree\Rules\SpacingRule;
use Oaktree\Rules\TableRule;
use Oaktree\Rules\TransformRule;
use Oaktree\Rules\TransitionsAnimationRule;
use Oaktree\Rules\TypographyRule;

class RuleLoader
{
    /**
     * @return RuleInterface[]
     */
    public function getRules(): array
    {
        return [
            new LayoutRule,
            new FlexboxGridRule,
            new TransformRule,
            new TransitionsAnimationRule,
            new SizingRule,
            new SpacingRule,
            new BackgroundRule,
            new TypographyRule,
            new BorderRule,
            new ListRule,
            new FormRule,
            new EffectRule,
            new FiltersRule,
            new InteractivityRule,
            new ContainerRule,
            new AnchorRule,
            new TableRule,

            // Components
            new ButtonComponet,
            new MessageComponent,
            new DialogComponent,
        ];
    }
}
