<?php

declare(strict_types=1);

namespace Exports\Controller;

use Exports\Controller\AppController;

/**
 * Exports Controller
 *
 * 
 */
class ExportsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authorization->authorizeModel("index", "add");

        $this->Authentication->allowUnauthenticated([
            "awardsByDomain"
        ]);
    }

    /**
     * export function
     * $data <table data>
     * $config <{columns => [{
     *                      "name"      =>  <"">,
     *                      "key"       =>  <mixed value>,
     *                      "isTime"    =>  <bool>,
     *                      },...]}>
     *                      
     * 
     */

    public function test() {}


    public function export($data, $config)
    {
        $this->Authorization->skipAuthorization();
        $this->autoRender = false;

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export-' . date("Y-m-d-h-i-s") . '.csv');
        $output = fopen('php://output', 'w');

        $columnNames = [];
        foreach ($config as $column) {
            $columnNames[] = $column->name;
        }


        fputcsv($output, $columnNames);
        $data = $data->toArray();

        if (count($data) > 0) {
            foreach ($data as $entry) {

                $row = [];

                foreach ($config->columns as $column) {
                    if ($row[$column->isDate]) {
                        $row[$column->key]->i18nFormat('MM-dd-yyyy');
                    } else {
                        $row[$column->key];
                    }
                }

                fputcsv($output, $row);
            }
        }
    }
}