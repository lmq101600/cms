<h3 class="page-title">
	<script type="text/javascript" src="<?=static_url('global/js/jquery.qrcode.min.js')?>"></script>
	<script src="<?=static_url('global/js/md5.min.js')?>" type="text/javascript"></script>
	<script src="<?=static_url('js/user.js')?>" type="text/javascript"></script>
	<?php
	echo $_current['mname'];
	?>
</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<?php
			echo $_current['mname'];
			?>
			<i class="fa fa-angle-right"></i>
		</li>
		<!-- 		<li>
					Data Tables
					<i class="fa fa-angle-right"></i>
				</li> -->
	</ul>
</div>
<div class="note note-success">
	<p>
		�û�ӵ�ж��Ȩ����Ͷ���Ȩ��ʱ��ȡ������
	</p>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue-hoki animated fadeInRight">
			<div class="portlet-title">
				<div class="caption">
					��¼�û��б�
				</div>
			</div>
			<div class="portlet-body">
				<div class="row table-toolbar">
					<div class="col-md-6">
						<div class="btn-group">
							<button class="btn green" id="addUserButton">
								����û� <i class="fa fa-plus"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
						<tr>
							<th>��¼��</th>
							<th>�ǳ�</th>
							<th>����</th>
							<th>�ȸ���֤������</th>
							<th>״̬</th>
							<th>����ʱ��</th>
							<th>���¸���ʱ��</th>
							<th>�޸��û���Ϣ</th>
							<th>�޸�����</th>
							<th>Ȩ��</th>
							<th>ɾ���û�</th>
						</tr>
						</thead>
						<tbody>
						<?php
						if (! empty($list)) {
							$arrStatus = getTableColumnInfo("user" ,'status' ,'colmunvalue');
							$arrLevel = getTableColumnInfo("user" ,'user_level' ,'colmunvalue');
							foreach ($list as $key => $value) {
								?>
								<tr>
									<td><?php echo $value['username'];?></td>
									<td><?php echo $value['nick_name'];?></td>
									<td><?php echo ! empty($arrLevel[$value['user_level']]) ? $arrLevel[$value['user_level']] : "";?></td>
									<td>
										<?php
										if (!empty($value['gcode'])) {
											?>
											<button class="btn btn-default btn-xs getcodeimg"  aid="<?=$value['gcode']?>"><?=$value['gcode']?></button>
											<?php
										}
										?>

									</td>
									<td>
										<?php
										echo ! empty($arrStatus[$value['status']]) ? $arrStatus[$value['status']] : "";
										?>
									</td>
									<td><?php echo $value['ctime'];?></td>
									<td><?php echo $value['mtime'];?></td>
									<td><button aid="<?php echo $value['id'];?>" class="btn blue-hoki btn-xs changeuserinfo">
											<i class="fa fa-edit"></i> �޸��û���Ϣ</button></td>
									<td><button aid="<?php echo $value['id'];?>" class="btn purple btn-xs changepwd">
											<i class="fa fa-lock"></i> �޸�����</button></td>
									<td>
										<?php
										if($value['user_level'] != 4)
										{
											?>
											<a class="btn blue-madison btn-xs" href="/m/user/editright?id=<?php echo $value['id'];?>">
												<i class="fa fa-edit"></i>
												�鿴�༭</a>
											<?php
										}
										?>
									</td>
									<td>
										<button aid="<?php echo $value['id'];?>" class="btn btn-danger delete btn-xs">
											<i class="fa fa-trash-o"></i>
											ɾ��
										</button>
									</td>
								</tr>
								<?php
							}
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>