<?php
declare(strict_types=1);

namespace Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion;

use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Messaging\Attribute\Parameter\Headers;
use Ecotone\Modelling\Attribute\EventHandler;

class Playground
{
    public array $headers = [];
    public ?Status $singleHeaderWithTypehint = null;
    public ?Status $singleHeaderWithoutTypehint = null;

    #[EventHandler('message')]
    public function eventHandler(
        #[Headers] array $headers,
        #[Header('withoutTypeHint')] $singleHeaderWithoutTypehint,
        #[Header('withTypeHint')] Status $singleHeaderWithTypehint,
    ): void {
        $this->headers = $headers;
        $this->singleHeaderWithTypehint = $singleHeaderWithTypehint;
        $this->singleHeaderWithoutTypehint = $singleHeaderWithoutTypehint;
    }
}
