<?php

namespace Tests;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use YD\Exception\MigrationException;
use YD\Migration;

/**
 * Class MigrationTest
 *
 * Migration::run() calls exit when nothing is pending; a separate process
 * per test turns such an exit into a failure instead of a silently cut suite.
 *
 * @category Tests
 * @package  Tests
 * @author   Samet TEMIZER <nestisamet@gmail.com>
 * @license  MIT License
 * @link     https://stemizer.com
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 */
class MigrationTest extends TestCase
{
    /**
     * Migration scripts in expected execution order
     */
    const SCRIPTS = [
        '0-create-tbl-foo.sql',
        '1-modify-tbl-foo.sql',
        '2-another-migration.sql',
        '2-foo-new-fields.sql',
        '3-foo-new-index.sql',
        '4-foo-new-index-a.sql',
        '5-foo-new-index-b.sql',
        '6-foo-new-index-c.sql',
        '7-foo-new-index-d.sql',
        '8-foo-new-index-x.sql',
        '9-foo-new-index-y.sql',
        '10-foo-new-index-z.sql',
    ];

    private $_dir;
    private $_executed = [];
    private $_recorded = [];

    /**
     * Scripts are written last-to-first with mtimes likewise, so neither
     * alphabetical nor file time order matches the expected one.
     * Each script's content is its own name.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->_dir = sys_get_temp_dir() . '/php-migration-' . uniqid();
        mkdir($this->_dir);
        foreach (array_reverse(self::SCRIPTS) as $i => $script) {
            file_put_contents("{$this->_dir}/{$script}", $script);
            touch("{$this->_dir}/{$script}", 1600000000 + $i);
        }
    }

    /**
     * Removes the scripts directory
     *
     * @return void
     */
    protected function tearDown(): void
    {
        array_map('unlink', glob("{$this->_dir}/*"));
        rmdir($this->_dir);
    }

    /**
     * Empty database: every script runs and is recorded, 9-* before 10-*
     *
     * @return void
     */
    public function testRunsScriptsInNaturalOrderOfFileNames(): void
    {
        (new Migration($this->_pdo(null), $this->_dir))->run();

        $this->assertSame(self::SCRIPTS, $this->_executed);
        $this->assertSame(self::SCRIPTS, $this->_recorded);
    }

    /**
     * 0-* .. 9-* applied as #0..#10, then 10-* added: only it runs, as #11
     *
     * @return void
     */
    public function testRunsOnlyScriptsAfterLastAppliedIndex(): void
    {
        (new Migration($this->_pdo(10), $this->_dir))->run();

        $this->assertSame(['10-foo-new-index-z.sql'], $this->_executed);
        $this->assertSame([11 => '10-foo-new-index-z.sql'], $this->_recorded);
    }

    /**
     * A wrong scripts path must fail, not pass for "nothing to run"
     *
     * @return void
     */
    public function testRejectsScriptsPathThatIsNotADirectory(): void
    {
        $this->expectException(MigrationException::class);

        new Migration($this->_pdo(null), "{$this->_dir}/missing");
    }

    /**
     * PDO double: `_migration` exists, its highest index is $lastApplied
     * (null: empty); captures executed scripts and written `_migration` rows
     *
     * @param int|null $lastApplied highest applied index
     *
     * @return PDO
     */
    private function _pdo($lastApplied)
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('rowCount')->willReturn(1);
        $statement->method('fetch')->willReturn(
            $lastApplied === null ? false : ['i' => $lastApplied]
        );

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(
            function ($sql) use ($statement) {
                if (in_array($sql, self::SCRIPTS, true)) {
                    $this->_executed[] = $sql;
                }
                return $statement;
            }
        );
        $pdo->method('exec')->willReturnCallback(
            function ($sql) {
                $row = "/values\s*\(\s*(\d+)\s*,\s*'([^']*)'\s*\)/i";
                if (preg_match($row, $sql, $match)) {
                    $this->_recorded[(int) $match[1]] = $match[2];
                }
                return 1;
            }
        );
        return $pdo;
    }
}
