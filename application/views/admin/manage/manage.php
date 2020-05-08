<h3 class="page-title">
	<?php echo $_current['mname'];?>
</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<?php echo $_current['mname'];?>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="alert alert-success">
			<strong>一级目录管理</strong>
			<a class="btn btn-xs green pull-right first_menu" href="#">
				<i class="fa fa-plus"></i> 添加一级目录
			</a>
			<a class="btn btn-xs blue-madison pull-right sort_first_menu" href="#" style="margin-right: 20px;" sort="1">
				<i class="fa fa-sort-alpha-asc"></i> 一级目录排序
			</a>
		</div>
		<ul class="ver-inline-menu tabbable margin-bottom-10 firstmenulist animated fadeInDown">
			<?php
			if(!empty($menulist))
			{
				$active = true;
				foreach ($menulist as $menu) {

					if(!empty($_COOKIE['active']))
					{
						$active = $_COOKIE['active']=="#tab_".$menu['id']?true:false;
					}
					?>
					<li class='<?php echo $active?"active":"";?>'>
						<a data-toggle="tab" aname="<?php echo $menu['mname'];?>" aid="<?php echo $menu['id'];?>" href="#tab_<?php echo $menu['id'];?>" aria-expanded="true">
							<i style="top:0px" class="<?php echo $menu['icon'];?>"></i> <span><?php echo $menu['mname'];?></span>
							<i style="top:0px" class="icon-plus pull-right second_menu popovers" data-content="没有子目录自动作为访问目录，拥有子目录自动作为分类目录" data-original-title="添加子目录" data-container="body" data-trigger="hover"></i>
							<i style="top:0px" class="icon-trash pull-right delete_first_menu popovers" data-content="拥有子目录时，无法删除。自动删除已配置权限" data-original-title="删除当前目录" data-container="body" data-trigger="hover"></i>
							<i style="top:0px" class="icon-note pull-right first_menu" title="修改当前目录"></i>
							<i style="top:0px" class="icon-equalizer pull-right sort_first_menu" title="排序"></i>
						</a>
						<span class='<?php echo $active?"after":"";?>'>
				</span>
					</li>
					<?php
					$active = false;
				}
			}
			?>
		</ul>
	</div>
	<div class="col-md-8">
		<div class="alert alert-warning">
			<strong>二级目录或权限管理</strong>
		</div>
		<div class="tab-content  animated fadeInRight">
			<?php
			if(!empty($menulist))
			{
				$arrStyle = array(
					"default",
					"success",
					"warning",
					"danger",
				);
				$active = true;
				foreach ($menulist as $menu) {
					if(!empty($_COOKIE['active']))
					{
						$active = $_COOKIE['active']=="#tab_".$menu['id']?true:false;
					}
					?>
					<div id="tab_<?php echo $menu['id'];?>" class="tab-pane <?php echo $active?"active":"";?>">
						<?php
						if(!empty($menu['_list']))
						{
							$arrStatus = getTableColumnInfo("plat_menu" ,'status' ,'colmunvalue');
							foreach ($menu['_list'] as $v) {
								?>
								<div class="alert alert-info " style="padding: 0px;overflow: hidden;"  aname="<?php echo $v['mname'];?>" aid="<?php echo $v['id'];?>" atype="edit">
					<span style="position: relative;top: 8px;padding-left:5px;">
						<i class="<?php echo $v['icon'];?>"></i> <?php echo trim($v['mname']);?>
					</span>
									<a href="#" class="btn purple-plum pull-right add_action_menu" title="添加权限" style="margin-left: 5px;">
										<i class="fa fa-plus"></i> 添加
									</a>
									<a href="#" class="btn red-sunglo pull-right delete_first_menu" title="删除目录" style="margin-left: 5px;">
										<i class="fa fa-trash-o"></i> 删除
									</a>

									<a href="#" class="btn btn-primary pull-right second_menu" title="修改当前目录" style="margin-left: 5px;">
										<i class="fa fa-edit" ></i> 修改
									</a>
								</div>
								<table class="table table-hover">
									<thead>
									<tr>
										<th>权限名称</th>
										<th>权限别名</th>
										<th>操作</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>当前目录权限</td>
										<td><?php echo $v['action'];?></td>
										<td></td>
									</tr>
									<?php
									if(!empty($actionlist[$v['id']]))
									{
										foreach ($actionlist[$v['id']] as  $action) {
											?>
											<tr>
												<td><?php echo $action['mname'];?></td>
												<td><?php echo $action['action'];?></td>
												<td aid="<?php echo $action['id'];?>"  aname="<?php echo $action['mname'];?>">
													<button class="btn btn-xs btn-danger delete_first_menu">
														<i class="fa fa-trash-o"></i>
														删除</button>
												</td>
											</tr>
											<?php
										}
									}
									?>
									</tbody>
								</table>
								<?php
							}
						}else{
							?>
							<div aname="<?php echo $menu['mname'];?>" aid="<?php echo $menu['id'];?>" >
								<a href="#" class="btn purple-plum pull-right add_action_menu" title="添加权限" style="margin-left: 5px;">
									<i class="fa fa-plus"></i> 添加
								</a>
							</div>
							<table class="table table-hover">
								<thead>
								<tr>
									<th>权限名称</th>
									<th>权限别名</th>
									<th>操作</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>当前目录权限</td>
									<td><?php echo $menu['action'];?></td>
									<td></td>
								</tr>
								<?php
								if(!empty($actionlist[$menu['id']]))
								{
									foreach ($actionlist[$menu['id']] as  $action) {
										?>
										<tr>
											<td><?php echo $action['mname'];?></td>
											<td><?php echo $action['action'];?></td>
											<td aid="<?php echo $action['id'];?>"  aname="<?php echo $action['mname'];?>">
												<button class="btn btn-xs btn-danger delete_first_menu">删除</button>
											</td>
										</tr>
										<?php
									}
								}
								?>
								</tbody>
							</table>
							<?php

						}
						?>
					</div>
					<?php
					$active = false;
				}
			}
			?>
		</div>
	</div>
</div>