<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data = $this->db->join('t_lokasi', 't_logger.lokasi_logger=t_lokasi.idlokasi')->join('kategori_logger', 't_logger.kategori_log=kategori_logger.id_katlogger')->get('t_logger')->result_array();
		$date_now = date('Y-m-d H:i:s');
		$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
		foreach($data as $key => $val){
			$data[$key]['sumber'] = 'Bagong';
			$data[$key]['status'] = 'aktif';
			$waktu = $this->db->get_where($val['temp_data'], array('code_logger'=> $val['id_logger']))->row();
			$data[$key]['waktu'] = $waktu->waktu;
			if($waktu->waktu < $date ){
				$data[$key]['status'] = 'nonaktif';
			}

		}
		echo json_encode($data);
	}
	
	
	function tes()
	{
		$serverlink = "https://telemetri.jasatirta1.co.id:8006/apisvc/arr/send-data/910555471e145f3f6d01ced3b9d42171/";

		$waktu_arr = '2023-11-28 16:48';
		$dtarr = array(
			'ID' => 0,
			'CreatedAt' => "0001-01-01T00:00:00Z",
			'deviceid' => 2,
			'channelid' => -1,
			'idmap' => 'RF',
			'realvalue' => '0',
			'snaptime' => $waktu_arr . "+07"
		);
		$dtbattery = array(
			'ID' => 0,
			'CreatedAt' => "0001-01-01T00:00:00Z",
			'deviceid' => 2,
			'channelid' => -1,
			'idmap' => 'VCC',
			'realvalue' => 12.28,
			'snaptime' => $waktu_arr . '+07'
		);
		$dttemp = array(
			'ID' => 0,
			'CreatedAt' => "0001-01-01T00:00:00Z",
			'deviceid' => 2,
			'channelid' => -1,
			'idmap' => 'LOG_TEMP',
			'realvalue' => 29.6,
			'snaptime' => $waktu_arr . '+07'
		);
		$dthumidity = array(
			'ID' => 0,
			'CreatedAt' => "0001-01-01T00:00:00Z",
			'deviceid' => 2,
			'channelid' => -1,
			'idmap' => 'LOG_HUMID',
			'realvalue' => 55.7,
			'snaptime' => $waktu_arr . '+07'
		);
		$kirim = array(
			$dtarr,
			$dtbattery,
			$dttemp,
			$dthumidity
		);


		//echo json_encode($key);
		$kirim_arr = json_encode($kirim);
		$ch_arr = curl_init();  // initialize curl handle
		curl_setopt($ch_arr, CURLOPT_URL, $serverlink); // set url to post to
		curl_setopt($ch_arr, CURLOPT_FAILONERROR, 1); //Fail on error
		curl_setopt($ch_arr, CURLOPT_RETURNTRANSFER, 1); // return into a variable
		curl_setopt($ch_arr, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch_arr, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch_arr, CURLOPT_POSTFIELDS, $kirim_arr); // add POST fields
		//curl_setopt($ch_vnotch, CURLOPT_SSL_VERIFYPEER, TRUE);
		$result_arr = curl_exec($ch_arr); // run the whole process
		curl_close($ch_arr);
		echo $result_arr;
		echo 'cek';
	}
	
	public function input_phone()
	{
		$data = $this->db->join('t_informasi','t_informasi.logger_id = t_logger.id_logger')->get('t_logger')->result_array();
		
		echo json_encode($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */