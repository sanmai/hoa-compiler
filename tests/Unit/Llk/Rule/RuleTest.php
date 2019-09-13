<?php
/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Tests\Hoa\Compiler\Unit\Llk\Rule;

use Hoa\Compiler\Llk\Rule\Rule;
use Tests\Hoa\Compiler\TestCase;

/**
 * Test suite of a rule.
 *
 * @covers \Hoa\Compiler\Llk\Rule\Rule
 */
class RuleTest extends TestCase
{
    private function makeRule(...$args): Rule
    {
        return new class(...$args) extends Rule {
            public function setChildrenProxy(...$args)
            {
                return parent::setChildren(...$args);
            }
        };
    }

    public function test_constructor()
    {
        $name     = 'foo';
        $children = ['bar'];

        $result = $this->makeRule($name, $children);

        $this->assertSame($name, $result->getName());
        $this->assertSame($children, $result->getChildren());
        $this->assertNull($result->getNodeId());
        $this->assertTrue($result->isTransitional());
    }

    public function test_constructor_with_node_id()
    {
        $name     = 'foo';
        $children = ['bar'];
        $nodeId   = 'baz';

        $result = $this->makeRule($name, $children, $nodeId);

        $this->assertSame($name, $result->getName());
        $this->assertSame($children, $result->getChildren());
        $this->assertSame($nodeId, $result->getNodeId());
        $this->assertTrue($result->isTransitional());
    }

    public function test_set_name_returns_old_name()
    {
        $name     = 'foo';
        $children = ['bar'];

        $rule = $this->makeRule($name, $children);
        $this->assertSame($name, $rule->setName('baz'));
    }

    public function test_get_name()
    {
        $name     = 'foo';
        $children = ['bar'];

        $rule = $this->makeRule($name, $children);
        $this->assertSame($name, $rule->getName());
    }

    public function test_set_children()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $this->assertSame(['bar'], $rule->setChildrenProxy(['baz']));
    }

    public function test_get_children()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);

        $children = ['bar'];
        $rule->setChildrenProxy($children);
        $this->assertSame($children, $rule->getChildren());
    }

    public function test_set_node_id()
    {
        $name        = 'foo';
        $children    = ['bar'];
        $nodeId      = 'id';
        $rule        = $this->makeRule($name, $children, $nodeId);

        $this->assertSame($nodeId, $rule->setNodeId('baz:qux'));
    }

    public function test_get_node_id()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setNodeId('baz');

        $this->assertSame('baz', $rule->getNodeId());
    }

    public function test_get_node_id_with_options()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setNodeId('baz:qux');

        $this->assertSame('baz', $rule->getNodeId());
    }

    public function test_get_node_options_empty()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setNodeId('baz');

        $this->assertSame([], $rule->getNodeOptions());
    }

    public function test_get_node_options()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setNodeId('baz:qux');

        $this->assertSame(['q', 'u', 'x'], $rule->getNodeOptions());
    }

    public function test_set_default_id()
    {
        $name        = 'foo';
        $children    = ['bar'];
        $nodeId      = 'id';
        $rule        = $this->makeRule($name, $children, $nodeId);

        $this->assertNull($rule->setDefaultId('baz:qux'));
    }

    public function test_get_default_id()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setDefaultId('baz');

        $this->assertSame('baz', $rule->getDefaultId());
    }

    public function test_get_default_id_with_options()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setDefaultId('baz:qux');

        $this->assertSame('baz', $rule->getDefaultId());
    }

    public function test_get_default_options_empty()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setDefaultId('baz');

        $this->assertSame([], $rule->getDefaultOptions());
    }

    public function test_get_default_options()
    {
        $name     = 'foo';
        $children = ['bar'];
        $rule     = $this->makeRule($name, $children);
        $rule->setDefaultId('baz:qux');

        $this->assertSame(['q', 'u', 'x'], $rule->getDefaultOptions());
    }

    public function test_set_pp_representation()
    {
        $name              = 'foo';
        $children          = ['bar'];
        $pp                = '<a> ::b:: c()?';
        $rule              = $this->makeRule($name, $children);
        $oldIsTransitional = $rule->isTransitional();

        $this->assertNull($rule->setPPRepresentation($pp));
        $this->assertTrue($oldIsTransitional);

        $this->assertFalse($rule->isTransitional());
    }

    public function test_get_pp_representation()
    {
        $name     = 'foo';
        $children = ['bar'];
        $pp       = '<a> ::b:: c()?';
        $rule     = $this->makeRule($name, $children);
        $rule->setPPRepresentation($pp);

        $this->assertSame($pp, $rule->getPPRepresentation());
    }

    public function test_is_transitional()
    {
        $name              = 'foo';
        $children          = ['bar'];
        $pp                = '<a> ::b:: c()?';
        $rule              = $this->makeRule($name, $children);
        $oldIsTransitional = $rule->isTransitional();
        $rule->setPPRepresentation($pp);

        $this->assertTrue($oldIsTransitional);
        $this->assertFalse($rule->isTransitional());
    }
}
