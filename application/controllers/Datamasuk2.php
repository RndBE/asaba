<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datamasuk2 extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->db = $this->load->database('demo', TRUE);
		$this->load->model('m_inputdata');
		$this->load->library('PhpMQTT');
		$this->load->library('Csvimport');
	}

	#################### --ARR--########################
	/* public function data_arr()
	{
		$data['data_arr']=$this->m_inputdata->view_arr($this->session->userdata('log_id'));
		$this->load->view('konten/inputdata/view_arr',$data);
	}
	*/
	public function data_awr()
	{
		if(empty($this->session->userdata('tgl_awr'))){
			$tgl=date('Y-m-d');
			$this->session->set_userdata('tgl_awr',$tgl);
		}
		$data['data_awr']= $this->db->query('SELECT * FROM awr where code_logger="'.$this->session->userdata('log_awr').'" and waktu like "'.$this->session->userdata('tgl_awr').'%" ORDER BY waktu desc')->result_array();
		$ky = [];
		if($data['data_awr']){
			foreach ($data['data_awr'][0] as $key => $vl) {
				$ky[] = ['key'=>$key];
			}
			$data['key'] = $ky;
		}else{
			$data['key'] = $ky;
		}

		foreach($data['key'] as $k=> $vl){
			$param = $this->db->where('kolom_sensor',$vl['key'])->where('logger_id',$this->session->userdata('log_awr'))->get('parameter_sensor')->row();
			if($param){
				$data['key'][$k]['nama'] = $param->nama_parameter;
			}else{
				$data['key'][$k]['nama'] = '';	

			}

		}
		$this->load->view('konten/inputdata/view_awr',$data);
	}
	function tgl_awr()
	{
		$date=date_create($this->input->post('tgl'));
		$tgl=date_format($date,"Y-m-d");
		$this->session->set_userdata('tgl_awr',$tgl);
		redirect('datamasuk/data_awr');
	}	
	public function add_awr()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;
		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,
			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
		);

		$this->m_inputdata->add_awr($data);
		$this->m_inputdata->update_tempawr($this->input->post('id_alat'),$data);

		//echo json_encode($data);
		if($this->input->post('sensor12') == '1')
		{
			$this->sinkrondatabyrequest($this->input->post('id_alat'));
		}

		if(!empty($this->input->post('sn')))
		{
			$query_inf=$this->db->query('select serial_number from t_informasi where logger_id = "'.$this->input->post('id_alat').'"');
			foreach($query_inf->result() as $inf)
			{

				if($inf->serial_number != $this->input->post('sn'))
				{
					$updata_inf = array(
						'serial_number'=>$this->input->post('sn'),

					);
					$this->db->where('logger_id', $this->input->post('id_alat'));
					$this->db->update('t_informasi', $updata_inf);
				}
			}
		}

	}

	public function data_awlr()
	{
		if(empty($this->session->userdata('tgl_awlr'))){
			$tgl=date('Y-m-d');
			$this->session->set_userdata('tgl_awlr',$tgl);
		}
		$data['data_awlr']= $this->db->query('SELECT * FROM awlr where code_logger="'.$this->session->userdata('log_awlr').'" and waktu >= "'.$this->session->userdata('tgl_awlr').' 00:00" and waktu <= "'.$this->session->userdata('tgl_awlr').' 23:59" ORDER BY waktu desc');

		$this->load->view('konten/inputdata/view_awlr',$data);
	}
	function tgl_awlr()
	{
		$date=date_create($this->input->post('tgl'));
		$tgl=date_format($date,"Y-m-d");
		$this->session->set_userdata('tgl_awlr',$tgl);
		redirect('datamasuk/data_awlr');
	}

	public function add_awlr()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;
		//$tma = $this->input->post('sensor20');
		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,

			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
			'sensor17'=>$this->input->post('sensor17'),
			'sensor18'=>$this->input->post('sensor18'),
			'sensor19'=>$this->input->post('sensor19'),
			'sensor20'=>$this->input->post('sensor20'),
			'sensor21'=>$this->input->post('sensor21'),
			'sensor22'=>$this->input->post('sensor22'),
			'sensor23'=>$this->input->post('sensor23'),

		);
		$this->db->insert('awlr',$data);
		$this->db->where('code_logger',$this->input->post('id_alat'))->update('temp_awlr',$data);
		$data_adr = array (
			'code_logger'=>'30001',
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,

			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
			'sensor17'=>$this->input->post('sensor17'),
			'sensor18'=>$this->input->post('sensor18'),
			'sensor19'=>$this->input->post('sensor19'),
			'sensor20'=>$this->input->post('sensor20'),
			'sensor21'=>$this->input->post('sensor21'),
			'sensor22'=>$this->input->post('sensor22'),
			'sensor23'=>$this->input->post('sensor23'),

		);
		$this->db->insert('rts',$data_adr);
		$update = $this->db->where('code_logger','30001')->update('temp_rts',$data_adr);
		echo json_encode($data_adr);
		$query_setsiaga=$this->db->query('select * from set_ews join t_logger on t_logger.id_logger = set_ews.logger_id where log_master="'.$this->input->post('id_alat').'"');
		if($query_setsiaga->num_rows() > 0 )
		{
			
			$list_ews = [];
			$send_ews = '';
			$tma_old = $this->db->get('temp_awlr')->row()->sensor20;
			foreach($query_setsiaga->result() as $set)
			{
				$log_master = $set->logger_id;
				$siaga1=$set->se1;
				$siaga2=$set->se2;
				$siaga3=$set->se3;
				
				$tma=$this->input->post('sensor20');
				if($tma >= $siaga3 && $tma < $siaga2){
					$sts = 'Siaga 3';
					$status='3';
					$send_ews = '{"con_'.$log_master.'":{"command":"con","setting":"ews","data":["3"]}}';
				}elseif($tma >=$siaga2 && $tma < $siaga1){
					$sts = 'Siaga 2';
					$status='2';
					$send_ews = '{"con_'.$log_master.'":{"command":"con","setting":"ews","data":["2"]}}';
				}elseif($tma >= $siaga1){
					$sts = 'Siaga 1';
					$status='1';
					$send_ews = '{"con_'.$log_master.'":{"command":"con","setting":"ews","data":["1"]}}';
				}
				else{
					$sts = 'Aman';
					$status='0';
					$send_ews = '{"con_'.$log_master.'":{"command":"con","setting":"ews","data":["0"]}}';
				}

				if($status != $set->status_siaga)
				{
					$logger_awlr=$this->input->post('id_alat');
					if($status == '0'){
						$dataupdatestat2=array(
							'status_siaga'=>$status,
							'status_speaker'=>'4'
						);
					}else{
						$dataupdatestat2=array(
							'status_siaga'=>$status,
							'status_speaker'=>'5'
						);
					}
					$this->db->where('logger_id', $set->logger_id)->update('set_ews',$dataupdatestat2);
					$log_history = [
						'id_logger'=>$set->logger_id,
						'nilai'=>$this->input->post('sensor1'),
						'status'=>$sts,
						'waktu'=>$waktu
					];
					$this->db->insert('log_siaga',$log_history);
				}
				$list_ews[] = [
					'nama_ews' =>$set->nama_logger,
					'status' =>$sts,
				];
			}
		}
		
		$ews_mqtt = json_decode($send_ews);
		$server = 'mqtt.beacontelemetry.com';     
		$port = 8883;                     
		$username = 'userlog';                   
		$password = 'b34c0n';                   
		$client_id = 'bemqtt-'.$this->input->post('id_alat'); 
		$ca="/etc/ssl/certs/ca-bundle.crt";

		$mqtt = new phpMQTT($server, $port, $client_id,$ca);

		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish($this->input->post('id_alat'), json_encode($data), 0, false);
			$mqtt->publish('EWS_Demo_Brantas', json_encode($ews_mqtt), 0, false);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}
	}

	public function sesi_loggerawlr()
	{
		$this->session->set_userdata('log_awlr',$this->input->post('logger_id'));
		redirect ('datamasuk/data_awlr');
	}

	public function sesi_loggerawr()
	{
		$this->session->set_userdata('log_awr',$this->input->post('logger_id'));
		redirect ('datamasuk/data_awr');
	}
	public function tes_add(){
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$tabel = $this->input->post('tabel');
		$waktu = $tanggal.' '.$jam;
		$data = array(
			'id_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,

			'tma'=>$this->input->post('sensor1'),
		);
		$this->tes_notif($data,$tabel,$tanggal,$jam);
	}	

	public function tes_notif($awlr, $tabel,$tanggal,$jam){
		if($tabel == 'awlr'){
			$data = $this->db->get('klasifikasi_tma')->result_array();
			foreach($data as $key=>$val){
				if($val['idlogger'] == $awlr['id_logger']){
					if($awlr['tma'] >= $val['siaga2'] and $awlr['tma'] < $val['siaga1']){

						$data2 = array(
							'id_logger' => $val['idlogger'],
							'status'=> 'Waspada',
							'waktu'=>$awlr['waktu'],
							'tma'=>$awlr['tma'],
							'warna'=> 'FCE22A',
							'tabel'=> $tabel
						);
						$this->db->insert('t_notif', $data2);
					}elseif($awlr['tma'] >= $val['siaga1']){
						$data2 = array(
							'id_logger' => $val['idlogger'],
							'status'=> 'Siaga',
							'waktu'=>$awlr['waktu'],
							'tma'=>$awlr['tma'],	
							'warna'=> 'F94A29',
							'tabel'=> $tabel
						);
						$this->db->insert('t_notif', $data2);
					}
				}
			}
		}else{
			$gabung = $tanggal . ' ' . $jam;
			$jam_awal = $tanggal . ' '. date('H',strtotime($gabung)).':00:00';
			$jam_akhir = $tanggal . ' '.date('H',strtotime($gabung)).':59:00';
			$data2 = $this->db->query("SELECT SUM(sensor1) as 'akm' FROM arr where code_logger='". $awlr['id_logger']."' and waktu >= '".$jam_awal."' and waktu <= '".$jam_akhir."' order by waktu asc")->row();
			$data3 = array();
			$data = $this->db->where('waktuper', 'perjam')->get('klasifikasi_hujan')->row();
			if($data2->akm >= $data->kuning and $data2->akm < $data->oranye){
				$data3 = array(
					'id_logger' => $awlr['id_logger'],
					'status'=> 'Hujan Sedang',
					'waktu'=>$awlr['waktu'],
					'tma'=>$data2->akm,
					'warna'=> 'FCE22A',
					'tabel'=> $tabel
				);
				$this->db->insert('t_notif', $data3);
			}elseif($data2->akm >= $data->oranye and $data2->akm < $data->merah){
				$data3 = array(
					'id_logger' => $awlr['id_logger'],
					'status'=> 'Hujan Lebat',
					'waktu'=>$awlr['waktu'],
					'tma'=>$data2->akm,
					'warna'=> 'f7963a',
					'tabel'=> $tabel
				);
				$this->db->insert('t_notif', $data3);
			}elseif($data2->akm >= $data->merah){
				$data3 = array(
					'id_logger' => $awlr['id_logger'],
					'status'=> 'Hujan Sangat Lebat',
					'waktu'=>$awlr['waktu'],
					'tma'=>$data2->akm,
					'warna'=> 'F94A29',
					'tabel'=> $tabel
				);
				$this->db->insert('t_notif', $data3);
			}
		}

		//$this->db->insert('t_notif', $awlr);

	}

	public function tes_akumulasi_arr(){
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$gabung = $tanggal . ' ' . $jam;
		$jam_awal = $tanggal . ' '. date('H',strtotime($gabung)).':00:00';
		$jam_akhir = $tanggal . ' '.date('H',strtotime($gabung)).':59:00';
		$data2 = $this->db->query("SELECT SUM(sensor1) as 'akm' FROM arr where code_logger='10124' and waktu >= '".$jam_awal."' and waktu <= '".$jam_akhir."' order by waktu asc")->row();
		$data3 = array();
		$data = $this->db->where('waktuper', 'perjam')->get('klasifikasi_hujan')->row();
		if($data2->akm >= $data->kuning and $data2->akm < $data->oranye){
			$data3 = array(
				'id_logger' => $awlr['id_logger'],
				'status'=> 'Hujan Sedang',
				'waktu'=>$awlr['waktu'],
				'tma'=>$awlr['tma'],
				'warna'=> 'fef21f',
				'tabel'=> $tabel
			);

		}elseif($data2->akm >= $data->oranye and $data2->akm < $data->merah){
			$data3 = array(
				'id_logger' => $awlr['id_logger'],
				'status'=> 'Hujan Lebat',
				'waktu'=>$awlr['waktu'],
				'tma'=>$awlr['tma'],
				'warna'=> 'f7963a',
				'tabel'=> $tabel
			);
		}elseif($data2->akm >= $data->merah){
			$data3 = array(
				'id_logger' => $awlr['id_logger'],
				'status'=> 'Hujan Sangat Lebat',
				'waktu'=>$awlr['waktu'],
				'tma'=>$awlr['tma'],
				'warna'=> 'ed1c24',
				'tabel'=> $tabel
			);
		}
		echo json_encode($data3);
		//echo $jam_awal;
		//$data = $this->db->query('')
	}
	##############################---EWS -----##########################

	public function data_ews()
	{
		if(empty($this->session->userdata('tgl_ews'))){
			$tgl=date('Y-m-d');
			$this->session->set_userdata('tgl_ews',$tgl);
		}
		$data['data_ews']= $this->db->query('SELECT * FROM ews where code_logger="'.$this->session->userdata('log_id').'" and waktu like "'.$this->session->userdata('tgl_ews').'%" ORDER BY waktu desc');

		$this->load->view('konten/inputdata/view_ews',$data);
	}
	function tgl_ews()
	{
		$date=date_create($this->input->post('tgl'));
		$tgl=date_format($date,"Y-m-d");
		$this->session->set_userdata('tgl_ews',$tgl);
		redirect('datamasuk/data_ews');
	}

	public function add_ews()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;

		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,

			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),

		);

		$this->db->insert('ews',$data);
		$this->db->where('code_logger',$this->input->post('id_alat'))->update('temp_ews',$data);

	}

	public function sesi_loggerews()
	{
		$this->session->set_userdata('log_id',$this->input->post('logger_id'));
		redirect ('datamasuk/data_ews');
	}



	############################################################################
	function cek_sinkron()
	{
		$tanggal=$this->input->post('tanggal');
		$jam=$this->input->post('jam');
		$koded=$this->input->post('kode_database');
		$nilai=$this->input->post('nilai');
		$kodep=$this->input->post('kode_penyedia');
		$model=$this->input->post('model');

		$datakirim=array(
			'tanggal' => $this->input->post('tanggal'),
			'jam' => $this->input->post('jam'),
			'idlogger' => $this->input->post('kode_database'),
			'nilai' =>$this->input->post('nilai')

		);
		$data=array( 'data' =>json_encode($datakirim));
		$this->m_inputdata->add_sinkron($datakirim);

	}

	function sinkrondatabyrequest($idlogger) {
		$cek_sinkron = $this->db->query('select * from set_sinkronisasi where idlogger = "'.$idlogger.'"');
		$set = $cek_sinkron->row();
		$date=date_create($set->tanggal);
		$tanggal = date_format($date,'Ymd');
		$file_name = $set->idlogger.'-'.$tanggal.'.csv';

		//$tabel = $this->input->get('tabel');
		$file_path =  './filelogger/'.$file_name;
		$idlogger = substr( $file_name, 0, 5 );

		if(file_exists($file_path))
		{
			$ceklogger=$this->db->query('select * from t_logger INNER JOIN kategori_logger ON t_logger.kategori_log=kategori_logger.id_katlogger where id_logger = "'.$idlogger.'"');

			if($ceklogger->num_rows() == 0)
			{
				//$cek = $ceklogger->row();
				//$tabel = $cek->tabel;
				$tabel = 't_demo';
			}
			else{

				$cek = $ceklogger->row();
				$tabel = $cek->tabel;
			}

			if ($this->csvimport->parse_file($file_path)) {
				$csv_array = $this->csvimport->parse_file($file_path);

				foreach ($csv_array as $row) {

					$cekdata=$this->db->query('select waktu,code_logger from '.$tabel.'  where code_logger="'.$row['id_alat'].'" and waktu = "'.$row['tanggal'].' '.$row['jam'].'"');
					//$cekdata=$this->db->query('select * from '.$tabel.'  where code_logger="'.$idlogger.'" and waktu = "'.$row['tanggal'].' '.$row['jam'].'"');
					if($cekdata->num_rows() == 0)
					{
						$insert_data = array(

							'code_logger'=>$row['id_alat'],
							'waktu'=>$row['tanggal'].' '.$row['jam'],
							'sensor1'=>$row['sensor1'],
							'sensor2'=>$row['sensor2'],
							'sensor3'=>$row['sensor3'],
							'sensor4'=>$row['sensor4'],
							'sensor5'=>$row['sensor5'],
							'sensor6'=>$row['sensor6'],
							'sensor7'=>$row['sensor7'],
							'sensor8'=>$row['sensor8'],
							'sensor9'=>$row['sensor9'],
							'sensor10'=>$row['sensor10'],
							'sensor11'=>$row['sensor11'],
							'sensor12'=>$row['sensor12'],
							'sensor13'=>$row['sensor13'],
							'sensor14'=>$row['sensor14'],
							'sensor15'=>$row['sensor15'],
							'sensor16'=>$row['sensor16'],
						);
						//
						$this->m_inputdata->insert_ftp($insert_data,$tabel);

					}
					else{
						echo 'Data sudah ada';
					}

				}


				//echo 'Berhasil sinkron data';

			} else {
				echo 'gagal parsing';
			}
			$data_update = array(
				'tanggal' => '0',
			);
			$this->m_inputdata->update_set($idlogger,$data_update);
			unlink($file_path);


		}
		else {
			echo 'File tidak ditemukan';
		}
	}

	######################## END #######################
}
