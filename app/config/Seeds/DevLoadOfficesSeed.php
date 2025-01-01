<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Offices seed.
 */
class DevLoadOfficesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Kingdom Seneschal',
                'department_id' => 1,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'reports_to_id' => NULL,
                'applicable_branch_types' => '"Kingdom"',
                'grants_role_id' => NULL,
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'Kingdom Deputy Seneschal',
                'department_id' => 1,
                'requires_warrant' => 0,
                'required_office' => 0,
                'only_one_per_branch' => 0,
                'deputy_to_id' => 1,
                'grants_role_id' => NULL,
                'reports_to_id' => 1,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 3,
                'name' => 'Earl Marshal',
                'department_id' => 2,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => NULL,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 4,
                'name' => 'Kingdom Rapier Marshal',
                'department_id' => 2,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => 3,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 5,
                'name' => 'Kingdom Chivalric Marshal',
                'department_id' => 2,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => 3,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 6,
                'name' => 'Kingdom Hospitaller',
                'department_id' => 3,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => NULL,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 7,
                'name' => 'Regional Hospitaller',
                'department_id' => 3,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => 6,
                'applicable_branch_types' => '"Principality","Region"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 8,
                'name' => 'Hospitaller',
                'department_id' => 3,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => 7,
                'applicable_branch_types' => '"Local Group"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 9,
                'name' => 'Kingdom Chronicler',
                'department_id' => 4,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => NULL,
                'applicable_branch_types' => '"Kingdom"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
            [
                'id' => 10,
                'name' => 'Chronicler',
                'department_id' => 4,
                'requires_warrant' => 1,
                'required_office' => 1,
                'only_one_per_branch' => 1,
                'deputy_to_id' => NULL,
                'grants_role_id' => NULL,
                'reports_to_id' => 9,
                'applicable_branch_types' => '"Local Group"',
                'term_length' => 2,
                'modified' => '2024-06-10 15:32:53',
                'created' => '2024-06-10 15:32:53',
                'created_by' => 1,
                'modified_by' => 1,
                'deleted' => NULL,
            ],
        ];

        $table = $this->table('officers_offices');
        $table->insert($data)->save();
    }
}