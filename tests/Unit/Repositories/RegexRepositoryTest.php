<?php

namespace YorCreative\Scrubber\Tests\Unit\Repositories;

use Illuminate\Support\Collection;
use YorCreative\Scrubber\Repositories\RegexRepository;
use YorCreative\Scrubber\Tests\TestCase;

class RegexRepositoryTest extends TestCase
{
    #[Group('RegexRepository')]
    #[Group('Unit')]
    public function test_it_can_verify_that_all_regex_patterns_have_testable_counter_parts()
    {
        app(RegexRepository::class)->getRegexCollection()->each(function ($regexClass) {
            $hits = 0;

            $this->assertStringContainsString(
                config('scrubber.redaction'),
                app(RegexRepository::class)->checkAndSanitize($regexClass->getPattern(), config('scrubber.redaction'), $regexClass->getTestableString(), $hits)
            );

            $this->assertEquals(1, $hits);
        });
    }

    #[Group('RegexRepository')]
    #[Group('Unit')]
    public function test_it_can_sanitize_a_string_with_multiple_sensitive_pieces()
    {
        $hits = 0;

        $content = app(RegexRepository::class)->getRegexCollection()->get('google_api')->getTestableString()
            .' something something something '
            .app(RegexRepository::class)->getRegexCollection()->get('google_api')->getTestableString();

        $this->assertStringContainsString(
            config('scrubber.redaction'),
            app(RegexRepository::class)->checkAndSanitize(
                app(RegexRepository::class)->getRegexCollection()->get('google_api')->getPattern(),
                config('scrubber.redaction'),
                $content,
                $hits
            )
        );

        $this->assertEquals(2, $hits);
    }

    #[Group('RegexRepository')]
    #[Group('Unit')]
    public function test_it_can_receive_a_collection()
    {
        $this->assertInstanceOf(Collection::class, app(RegexRepository::class)->getRegexCollection());
    }

    #[Group('RegexRepository')]
    #[Group('Unit')]
    public function test_it_can_check_hits()
    {
        $content = app(RegexRepository::class)->getRegexCollection()->get('google_api')->getTestableString()
            .' something something something '
            .app(RegexRepository::class)->getRegexCollection()->get('google_api')->getTestableString();

        $hits = app(RegexRepository::class)->check(
            app(RegexRepository::class)->getRegexCollection()->get('google_api')->getPattern(),
            $content
        );

        $this->assertEquals(2, $hits);
    }
}
