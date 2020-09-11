<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('indicators');
        $this->load->model('historical');
    }

    public function index()
    {
        $this->loadView('app/index');
    }

    public function getCurrentValue(string $indCode)
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $indicator = $this->indicators->getEntry(array('code' => $indCode));

        $currentValue = $this->historical->getEntry(array('indicator' => $indicator->id), array('date' => 'DESC'));

        $currentValue->measurement = $indicator->measurement;
        $currentValue->indicator = $indicator->name;
        $currentValue->code = $indicator->code;
        $currentValue->date = date('d-M-Y', strtotime($currentValue->date));

        unset($currentValue->id);

        echo json_encode($currentValue);
    }

    public function getHistorical(string $indCode, string $from = null, string $to = null)
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $indicator = $this->indicators->getEntry(array('code' => $indCode));

        $filters = array('indicator' => $indicator->id);

        if (!is_null($from) && $from != '0')
            $filters['date>='] = $from;

        if (!is_null($to) && $to != '0')
            $filters['date<='] = $to;

        $historical = $this->historical->getEntries($filters);

        $data = array_map(function ($entry) {
            return array(
                't' => $entry->date,
                'y' => $entry->value
            );
        }, $historical);


        $config = array(
            'data' => array(
                'datasets' => [array(
                    'label' => $indicator->name,
                    'data' => $data,
                    'type' => 'line',
                    'pointRadius' => 0,
                    'fill' => false,
                    'lineTension' => 0,
                    'borderWidth' => 2,
                )]
            ),
            'options' => array(
                'elements' => array(
                    'line' => array(
                        'tension' => 0
                    )
                ),
                'animation' => array(
                    'duration' =>  0
                ),
                'hover' => array(
                    'animationDuration' => 0
                ),
                'responsiveAnimationDuration' => 0,
                'scales' => array(
                    'xAxes' => [array(
                        'type' => 'time',
                        'distribution' => 'series',
                        'offset' => true,
                        'bounds' => 'data',
                        'time' => array(
                            'tooltipFormat' => 'DD MMM YYYY',
                            'unit' => 'month'
                        ),
                        'ticks' => array(
                            'minRotation' => 50,
                            'maxRotation' => 50,
                            'sampleSize' => count($data) > 50 ? 50 : count($data)
                        )
                    )],
                    'yAxes' => [array(
                        'gridLines' => array(
                            'drawBorder' => false
                        ),
                        'scaleLabel' => array(
                            'display' => true,
                            'labelString' => $indicator->measurement
                        )
                    )]
                ),
                'tooltips' => array(
                    'intersect' => false,
                    'mode' => 'index',
                )
            )
        );

        echo json_encode($config);
    }

    public function ufcrud()
    {
        $data = array('indicator' => $this->indicators->getEntry(array('code' => 'uf')), 'noDropDown' => true);

        $this->loadView('app/ufcrud', $data);
    }

    public function getUfHistorical()
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $indicator = $this->indicators->getEntry(array('code' => 'uf'));

        $historical = $this->historical->getEntries(array('indicator' => $indicator->id), array('date' => 'DESC'));

        foreach ($historical as $entry)
            $entry->buttons = $this->genEditButton($entry->id) . ' ' . $this->genDeleteButton($entry->id);

        echo json_encode($historical);
    }


    public function saveUf()
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $input = $this->input->post();

        if ($input['value'] == "" || $input['date'] == '')
            die(json_encode(array('success' => false)));

        $id = $input['id'];
        unset($input['id']);

        $input['date'] = date('Y-m-d H:i:s', strtotime($input['date']) + 60 * 60); // ugly fix to avoid parsing errors when loading data to datatables

        $res = null;

        if ($id == "") {
            $indicator = $this->indicators->getEntry(array('code' => 'uf'));
            $input['indicator'] = $indicator->id;

            $res = $this->historical->saveEntry($input);
        } else
            $res = $this->historical->editEntry($input, array('id' => $id));

        echo json_encode(array('success' => $res == 1 ? true : false));
    }

    public function deleteUf($id)
    {
        if (!$this->input->is_ajax_request())
            show_404();

        $res = $this->historical->deleteEntry(array('id' => $id));


        echo json_encode(array('success' => $res == 1 ? true : false));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function loadView($view, $extra = null)
    {
        $data['indicators'] = $this->indicators->getEntries();

        if (!is_null($extra))
            $data = array_merge($data, $extra);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/nav', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer');
    }

    private function genEditButton($data)
    {
        return "<button class='btn btn-info btn-sm editUf' data-id='" . $data . "' data-toggle='modal' data-target='#ufModal'>Edit</button>";
    }

    private function genDeleteButton($data)
    {
        return "<button type='button' class='btn btn-sm btn-danger deleteUf' data-id='" . $data . "'>Delete</button>";
    }
}
