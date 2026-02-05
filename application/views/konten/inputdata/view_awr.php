<?php
$data20 =  $this->db->query('select count(DISTINCT waktu) as waktu from awr where code_logger="'.$this->session->userdata('log_awr').'" and waktu >= "'.  $this->session->userdata('tgl_awr').'  00:00" and  waktu <= "'.  $this->session->userdata('tgl_awr').'  23:59" ')->row();
$current_time = time();
$current_minute = date('i', $current_time);
$total_minutes = ((int)date('H', $current_time) * 60) + (int)$current_minute;
$data_count = $data20->waktu;
if ($this->session->userdata('tgl_awr') == date('Y-m-d')) {
	$tgl = date('Y-m-d H:i');

	if ($data_count > $total_minutes) {
		$data_count = $total_minutes;
	}
	$res = number_format(($data_count / $total_minutes * 100), 2);
	$res2 = $res . ' %';
} else {

	$tgl = $this->session->userdata('tgl_awr');
	$total_minutes = 1440;
	$res = number_format(($data_count / 1440 * 100), 2);
	$res2 = $res . ' %';
}

	$query_inf=$this->db->query('select * from t_informasi where logger_id = "'.$this->session->userdata('log_awr').'"');
	foreach($query_inf->result() as $inf)
	{
		$sn = $inf->serial_number ;
	}

?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bagong-AWR</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>
<body class="text-bg-light px-3">
	<main>
		<h3 class="fw-bold mb-3 mt-3 text-center">Data Input AWR</h3> 
				<hr/>
		<div class="container-md px-3">
			<section class="mb-3"> 
				
				<div class="row gx-md-3 justify-content-center "> 
					<div class="col-lg-4"> 
						<ul class="list-group list-group-item-primary p-0">
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">ID Logger</span>
								<span><?= $this->session->userdata('log_awr') ?></span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">Tanggal</span>
								<span><?= $this->session->userdata('tgl_awr') ?></span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">Serial Number</span>
								<span><?= (isset($sn)) ? $sn : '-' ?></span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">Kelengkapan Data</span>
								<span><?= $data_count .' / ' . $total_minutes ?></span>
							</li>
						</ul>
					</div> 
					<div class="col-lg-5"> 
						<div class="card mb-2" >
							<div class="card-body ">
								<!--<form class="row g-3">-->
								<?php echo form_open('datamasuk/sesi_loggerawr','class="row g-3 align-items-center "');?>
								<div class="col-3 ">
									<label class="text-md-start">ID Logger</label>
								</div>
								<div class="col-6">

									<input type="text" name="logger_id" class="form-control form-control-sm" value="<?= $this->session->userdata('log_awlr') ?>">
								</div>
								<div class="col-3">
									<div class="d-grid gap-2">
										<input class="btn btn-primary btn-sm" name="btnlog" value="Cari"  type="submit"/>
									</div>
								</div>
								<!--</form>-->
								<?php echo form_close();?>
							</div>
						</div>	
						<div class="card mb-2">
							<div class="card-body justify-content-between align-items-center">
								<!--<form class="row g-3">-->
								<?php echo form_open('datamasuk/tgl_awr','class="row g-3"');?>
								<div class="col-3">
									<label class="text-md-start">Tanggal</label>
								</div>
									<div class="col-6">
										
										<input type="date" id="tgl" name="tgl" class="form-control form-control-sm" value="<?= $this->session->userdata('tgl_awlr') ?>">
									</div>
									<div class="col-3">
										<div class="d-grid gap-2">
										<input class="btn btn-primary btn-sm" name="btntgl" value="Cari" type="submit"/>
										</div>
									</div>
								<!--</form>-->
								<?php echo form_close();?>
							</div>
						</div>
					
					</div> 
					
					<div class="col-lg-3 "> 
						<?php echo form_open('datamasuk/data_awr');?>
						<div class="d-grid gap-2 col-5  mx-auto">
							<button class="btn btn-primary btn-sm" name="btnrefresh" type="submit"><div class="d-flex justify-content-center"><span class="material-symbols-outlined mx-2" >refresh</span> Refresh</div></button>

						</div>
						<?php echo form_close();?>
					</div> 
			
				</div> 
			</section>
	
		</div>
	</main>
	<div class="container-fluid px-3">
		<div class="table-responsive">
			<table class="table table-sm table-bordered"> 
				<thead class="table-secondary fw-bold">
					<tr>
						<?php foreach ($key as $kesy => $value) { ?>
							<td style="font-size:9px;text-align:center;white-space:nowrap"><?= str_replace('_',' ',$value['nama']) ?></td>
						<?php } ?>
					</tr>
					<tr>
						<?php foreach ($key as $kesy => $value) { ?>
							<td style="font-size:14px;text-align:center"><?= $value['key'] ?></td>
						<?php } ?>
					</tr>
				</thead>

				<tbody class="h6">
					<?php
					foreach ($data_awr as $row) {
					?>
						<tr>
							<?php foreach ($row as $ky => $val) { ?>
								<td  style="font-size:14px;text-align:center" class="text-nowrap"><?= $val ?></td>
							<?php } ?>
						</tr>

					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
 
</body>
</html>