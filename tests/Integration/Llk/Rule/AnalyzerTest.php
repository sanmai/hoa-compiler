<?php
/**
 * Hoa
 *
 *
 * @license
 *
 * BSD 3-Clause License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its
 *    contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Tests\Hoa\Compiler\Integration\Llk\Rule;

use Hoa\Compiler as LUT;
use Hoa\Compiler\Llk\Rule;
use Hoa\Compiler\Llk\Rule\Analyzer as SUT;
use Tests\Hoa\Compiler\TestCase;

/**
 * Test suite of the rule analyzer.
 *
 * @covers \Hoa\Compiler\Llk\Rule\Analyzer
 * @uses \Hoa\Compiler\Llk\Lexer
 * @uses \Hoa\Compiler\Llk\Rule
 * @uses \Hoa\Compiler\Llk\Rule\Token
 * @uses \Hoa\Compiler\Llk\Rule\Repetition
 * @uses \Hoa\Compiler\Exception
 */
class AnalyzerTest extends TestCase
{
    /**
     * @test
     */
    public function case_simple_kept_token()
    {
        return $this->_case_simple_token(true);
    }

    /**
     * @test
     */
    public function case_simple_skipped_token()
    {
        return $this->_case_simple_token(false);
    }

    protected function _case_simple_token($kept)
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar']],
                $ruleA    = $kept ? '<foo>' : '::foo::',
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Token('ruleA', 'foo', null, -1, $kept),
                $_ruleA->setPPRepresentation($ruleA)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_simple_named()
    {
        $this
            ->given(
                $tokens = ['default' => ['foo' => 'bar']],
                $rules  = [
                    'ruleA' => 'ruleB()',
                    'ruleB' => '<foo>'
                ],
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Concatenation('ruleA', ['ruleB']),
                $_ruleA->setPPRepresentation($rules['ruleA']),
                $_ruleB = new Rule\Token('ruleB', 'foo', null, -1, true),
                $_ruleB->setPPRepresentation($rules['ruleB'])
            )
            ->when($result = $analyzer->analyzeRules($rules))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        'ruleA' => $_ruleA,
                        'ruleB' => $_ruleB
                    ]);
    }

    /**
     * @test
     */
    public function case_simple_kept_unified_token()
    {
        return $this->_case_simple_unified_token(true);
    }

    /**
     * @test
     */
    public function case_simple_skipped_unified_token()
    {
        return $this->_case_simple_unified_token(false);
    }

    protected function _case_simple_unified_token($kept)
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar']],
                $ruleA    = $kept ? '<foo[42]>' : '::foo[42]::',
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Token('ruleA', 'foo', null, 42, $kept),
                $_ruleA->setPPRepresentation($ruleA)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_repetition_zero_or_one()
    {
        return $this->_case_repetition('?', 0, 1);
    }

    /**
     * @test
     */
    public function case_repetition_one_or_more()
    {
        return $this->_case_repetition('+', 1, -1);
    }

    /**
     * @test
     */
    public function case_repetition_zero_or_more()
    {
        return $this->_case_repetition('*', 0, -1);
    }

    /**
     * @test
     */
    public function case_repetition_n_to_m()
    {
        return $this->_case_repetition('{7,42}', 7, 42);
    }

    /**
     * @test
     */
    public function case_repetition_n_or_more()
    {
        return $this->_case_repetition('{7,}', 7, -1);
    }

    /**
     * @test
     */
    public function case_repetition_exactly_n()
    {
        return $this->_case_repetition('{7}', 7, 7);
    }

    protected function _case_repetition($quantifier, $min, $max)
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar']],
                $ruleA    = '<foo>' . $quantifier,
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Repetition('ruleA', $min, $max, 0, null),
                $_ruleA->setPPRepresentation($ruleA),
                $_rule0 = new Rule\Token(0, 'foo', null, -1, true)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        '0'     => $_rule0,
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_concatenation()
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar', 'baz' => 'qux']],
                $ruleA    = '<foo> <baz>',
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Concatenation('ruleA', [0, 1], null),
                $_ruleA->setPPRepresentation($ruleA),
                $_rule0 = new Rule\Token(0, 'foo', null, -1, true),
                $_rule1 = new Rule\Token(1, 'baz', null, -1, true)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        0       => $_rule0,
                        1       => $_rule1,
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_choice()
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar', 'baz' => 'qux']],
                $ruleA    = '<foo> | <baz>',
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Choice('ruleA', [0, 1], null),
                $_ruleA->setPPRepresentation($ruleA),
                $_rule0 = new Rule\Token(0, 'foo', null, -1, true),
                $_rule1 = new Rule\Token(1, 'baz', null, -1, true)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        0       => $_rule0,
                        1       => $_rule1,
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_rule()
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar', 'baz' => 'qux']],
                $ruleA    = '<foo> | <baz>',
                $analyzer = new SUT($tokens),
                $_ruleA = new Rule\Choice('ruleA', [0, 1], null),
                $_ruleA->setPPRepresentation($ruleA),
                $_rule0 = new Rule\Token(0, 'foo', null, -1, true),
                $_rule1 = new Rule\Token(1, 'baz', null, -1, true)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        0       => $_rule0,
                        1       => $_rule1,
                        'ruleA' => $_ruleA
                    ]);
    }

    /**
     * @test
     */
    public function case_full()
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar', 'baz' => 'qux']],
                $ruleA    = '<foo>+ | ruleB() ::foo::',
                $ruleB    = '<baz> ruleA()',
                $analyzer = new SUT($tokens),
                $_rule0 = new Rule\Token(0, 'foo', null, -1, true),
                $_rule1 = new Rule\Repetition(1, 1, -1, 0, null),
                $_rule2 = new Rule\Token(2, 'foo', null, -1, false),
                $_rule3 = new Rule\Concatenation(3, ['ruleB', 2]),
                $_ruleA = new Rule\Choice('ruleA', [1, 3], null),
                $_ruleA->setPPRepresentation($ruleA),
                $_rule5 = new Rule\Token(5, 'baz', null, -1, true),
                $_ruleB = new Rule\Concatenation('ruleB', [5, 'ruleA']),
                $_ruleB->setPPRepresentation($ruleB)
            )
            ->when($result = $analyzer->analyzeRules(['ruleA' => $ruleA, 'ruleB' => $ruleB]))
            ->then
                ->array($result)
                    ->isRoughlyEqualTo([
                        0       => $_rule0,
                        1       => $_rule1,
                        2       => $_rule2,
                        3       => $_rule3,
                        'ruleA' => $_ruleA,
                        5       => $_rule5,
                        'ruleB' => $_ruleB
                    ]);
    }

    /**
     * @test
     */
    public function case_no_rule()
    {
        $this
            ->given($analyzer = new SUT([]))
            ->exception(function () use ($analyzer) {
                $analyzer->analyzeRules([]);
            })
                ->isInstanceOf(LUT\Exception::class)
                ->hasMessage('No rules specified!');
    }

    /**
     * @test
     */
    public function case_error_while_parsing_rule()
    {
        $this
            ->given(
                $tokens   = ['default' => ['foo' => 'bar']],
                $ruleA    = '<foo>{2,1}',
                $analyzer = new SUT($tokens)
            )
            ->exception(function () use ($analyzer, $ruleA) {
                $analyzer->analyzeRules(['ruleA' => $ruleA]);
            })
                ->isInstanceOf(LUT\Exception::class)
                ->hasMessage(
                    'Upper bound 1 must be greater or equal to lower bound ' .
                    '2 in rule ruleA.'
                );
    }

    /**
     * @test
     */
    public function case_kept_token_does_not_exist()
    {
        $this
            ->given(
                $tokens   = [],
                $ruleA    = '<foo>',
                $analyzer = new SUT($tokens)
            )
            ->exception(function () use ($analyzer, $ruleA) {
                $analyzer->analyzeRules(['ruleA' => $ruleA]);
            })
                ->isInstanceOf(LUT\Exception::class)
                ->hasMessage('Token <foo> does not exist in rule ruleA.');
    }

    /**
     * @test
     */
    public function case_skipped_token_does_not_exist()
    {
        $this
            ->given(
                $tokens   = [],
                $ruleA    = '::foo::',
                $analyzer = new SUT($tokens)
            )
            ->exception(function () use ($analyzer, $ruleA) {
                $analyzer->analyzeRules(['ruleA' => $ruleA]);
            })
                ->isInstanceOf(LUT\Exception::class)
                ->hasMessage('Token ::foo:: does not exist in rule ruleA.');
    }

    /**
     * @test
     */
    public function case_rule_does_not_exist()
    {
        $this
            ->given(
                $tokens   = [],
                $ruleA    = 'ruleB()',
                $analyzer = new SUT($tokens)
            )
            ->exception(function () use ($analyzer, $ruleA) {
                $analyzer->analyzeRules(['ruleA' => $ruleA]);
            })
                ->isInstanceOf(LUT\Exception::class)
                ->hasMessage(
                    'Cannot call rule ruleB() in rule ruleA because it does not exist.'
                );
    }
}
