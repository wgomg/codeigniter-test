<?php

class Migration_Initial extends CI_Migration
{
    private $urlLatest = "https://mindicador.cl/api";
    private $urlHistorical = "https://mindicador.cl/api/tipo_indicador/año";

    private $LOWEST_YEAR = 1927;    // the lowest year with data for any indicator

    public function up()
    {
        echo "\nRunning migrations:\n\n";
        echo "- Creating Indicator table...\n";

        // Create tables
        $this->dbforge->add_field(array(
            'id'    => array(
                'type'              => 'int',
                'auto_increment'    => true
            ),
            'code'  => array(
                'type'              => 'varchar',
                'constraint'        => '50',
                'unique'            => true
            ),
            'name'  => array(
                'type'              => 'varchar',
                'constraint'        => '50',
                'unique'            => true
            ),
            'measurement'  => array(
                'type'              => 'varchar',
                'constraint'        => '20'
            )
        ));

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('Indicators');

        echo "- Creating Historical table...\n";

        $historicalFields = array(
            'id'    => array(
                'type'              => 'int',
                'unsigned'          => true,
                'auto_increment'    => true
            )
        );

        if ($this->db->dbdriver === 'sqlsrv')
            array_push($historicalFields, 'indicator int NOT NULL FOREIGN KEY (indicator) REFERENCES Indicators(id)');

        if ($this->db->dbdriver === 'mysqli') {
            $historicalFields['indicator'] = array('type' => 'int');
            array_push($historicalFields, 'CONSTRAINT FOREIGN KEY (indicator) REFERENCES Indicators(id)');
        }

        $historicalFields = array_merge($historicalFields, array(
            'value'  => array(
                'type'              => 'float',
            ),
            'date'  => array(
                'type'              => 'datetime',
            )
        ));

        $this->dbforge->add_field($historicalFields);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('Historical');

        // Get indicators data
        if ($this->db->table_exists('Indicators')) {
            echo "- Filling up Indicators data...\n";

            $indPropsJson = json_decode(file_get_contents($this->urlLatest), true);
            $indPropsJson = array_filter($indPropsJson, function ($prop) {
                return is_array($prop);
            });
            $insertData = array();

            $progress = 0;
            $total = count($indPropsJson);

            foreach ($indPropsJson as $prop) {
                $this->progress_bar($progress++, $total);

                array_push($insertData, array(
                    'code'          => $prop['codigo'],
                    'name'          => $prop['nombre'],
                    'measurement'   => $prop['unidad_medida']
                ));
            }

            $this->progress_bar($progress++, $total);

            if (count($insertData) > 0)
                $this->db->insert_batch('Indicators', $insertData);

            $result = $this->db->get('Indicators');
            $indicators = $result->result_array();

            if ($this->db->table_exists('Historical') && count($indicators) > 0) {
                echo "\n- Filling up Historical data...\n";

                $now = date('c');
                $thisYear = date('Y', strtotime($now));

                $historicalData = array();

                $progress = 0;
                $total = count($indicators) * ($thisYear - $this->LOWEST_YEAR);

                foreach ($indicators as $ind) {
                    for ($year = $thisYear; $year > $this->LOWEST_YEAR; $year--) {
                        $url = str_replace(['tipo_indicador', 'año'], [$ind['code'], $year], $this->urlHistorical);

                        $json = json_decode(file_get_contents($url), true);
                        $yearData = $json['serie'];

                        $this->progress_bar($progress++, $total);

                        if (count($yearData) == 0) continue;

                        foreach ($yearData as $data) {
                            if ($data['fecha'] <= $now)
                                array_push($historicalData, array(
                                    'indicator' => $ind['id'],
                                    'value'     => $data['valor'],
                                    'date'      => $data['fecha']
                                ));
                        }
                    }
                }

                $this->progress_bar($progress++, $total);
            }

            if (count($historicalData) > 0)
                $this->db->insert_batch('Historical', $historicalData);
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('Historical');
        $this->dbforge->drop_table('Indicators');
    }

    //
    // https://gist.github.com/mayconbordin/2860547
    //
    private function progress_bar($progress, $total, $width = 50)
    {
        $perc = round(($progress * 100) / $total);
        $bar = round(($width * $perc) / 100);

        echo "\r | " . str_repeat("=", $bar) . ' ' . $perc . '% >';
    }
}
