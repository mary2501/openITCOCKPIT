<?php
// Copyright (C) <2015>  <it-novum GmbH>
//
// This file is dual licensed
//
// 1.
//	This program is free software: you can redistribute it and/or modify
//	it under the terms of the GNU General Public License as published by
//	the Free Software Foundation, version 3 of the License.
//
//	This program is distributed in the hope that it will be useful,
//	but WITHOUT ANY WARRANTY; without even the implied warranty of
//	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	GNU General Public License for more details.
//
//	You should have received a copy of the GNU General Public License
//	along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

// 2.
//	If you purchased an openITCOCKPIT Enterprise Edition you can use this file
//	under the terms of the openITCOCKPIT Enterprise Edition license agreement.
//	License agreement and license key will be shipped with the order
//	confirmation.
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-file-image-o fa-fw "></i>
            <?php echo __('Adhoc Reports'); ?>
            <span>>
                <?php echo __('Downtime report'); ?>
            </span>
        </h1>
    </div>
</div>

<div class="jarviswidget">
    <header>
        <span class="widget-icon"> <i class="fa fa-pencil-square-o"></i> </span>
        <h2><?php echo __('Create downtime report'); ?></h2>
        <div class="widget-toolbar" role="menu">
            <a ui-sref="DowntimereportsIndex" class="btn btn-default btn-xs" iconcolor="white">
                <i class="glyphicon glyphicon-white glyphicon-arrow-left"></i> <?php echo __('Back to list'); ?>
            </a>
        </div>
    </header>
    <div>
        <div class="widget-body">
            <?php
            echo $this->Form->create('Downtimereport', [
                'class' => 'form-horizontal clear',
            ]);
            ?>
            <div ng-class="{'has-error': errors.services}" ng-init="reportMessage=
            {successMessage : '<?php echo __('Report created successfully'); ?>' , errorMessage: '<?php echo __('Report could not be created'); ?>'}">
                <div class="form-group">
                    <label class="col-xs-1 col-md-1 col-lg-1 control-label">
                        <?php echo __('Evaluation'); ?>
                    </label>
                    <div class="col col-xs-10 col-md-10 col-lg-10">
                        <span>
                            <input type="radio"
                                   id="hosts"
                                   ng-model="post.evaluation_type"
                                   ng-value="0">
                            <label for="hosts">
                                <i class="fa fa-desktop"></i>
                                <?php echo __('Hosts'); ?>
                            </label>
                        </span>
                        <span class="padding-left-10">
                            <input type="radio"
                                   id="hostandservices"
                                   ng-model="post.evaluation_type"
                                   ng-value="1">
                            <label for="hostandservices">
                                <i class="fa fa-cogs"></i>
                                <?php echo __('Hosts and Services'); ?>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-1 col-md-1 col-lg-1 control-label">
                        <?php echo __('Report format'); ?>
                    </label>
                    <div class="col col-xs-10 col-md-10 col-lg-10">
                        <select
                                class="form-control"
                                ng-model="post.report_format">
                            <option ng-value="1"><?php echo __('PDF'); ?></option>
                            <option ng-value="2"><?php echo __('HTML'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col col-md-1 control-label">
                        <?php echo __('Timeperiod'); ?>
                    </label>
                    <div class="col col-xs-10 col-lg-10">
                        <select
                                data-placeholder="<?php echo __('Please choose a timeperiod'); ?>"
                                class="form-control"
                                chosen="timeperiods"
                                ng-options="timeperiod.Timeperiod.id as timeperiod.Timeperiod.name for timeperiod in timeperiods"
                                ng-model="post.timeperiod_id">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group required" ng-class="{'has-error': errors.from_time}">
                    <label class="col col-md-1 control-label"
                           for="FromTime"><?php echo __('From'); ?></label>
                    <div class="col col-xs-10 col-md-10 col-lg-10">
                        <input type="text" class="form-control" ng-model="post.from_time"
                               placeholder="<?php echo __('DD.MM.YYYY'); ?>">
                        <div ng-repeat="error in errors.from_time">
                            <div class="help-block text-danger">{{ error }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group required" ng-class="{'has-error': errors.to_time}">
                    <label class="col col-md-1 control-label"
                           for="ToTime"><?php echo __('To'); ?></label>
                    <div class="col col-xs-10 col-md-10 col-lg-10">
                        <input type="text" class="form-control" ng-model="post.to_time"
                               placeholder="<?php echo __('DD.MM.YYYY'); ?>">
                        <div ng-repeat="error in errors.to_time">
                            <div class="help-block text-danger">{{ error }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-1 col-md-1 col-lg-1 control-label">
                        <?php echo __('Reflection state'); ?>
                    </label>
                    <div class="col col-xs-10 col-md-10 col-lg-10">
                        <select class="form-control" ng-model="post.reflection_state">
                            <option ng-value="1"><?php echo __('soft and hard state'); ?></option>
                            <option ng-value="2"><?php echo __('only hard state'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="padding-bottom-10">
                <div class="row">
                    <div class="alert alert-info" ng-show="generatingReport">
                        <i class="fa fa-spin fa-refresh"></i>
                        <?php echo __('Generating report...'); ?>
                    </div>
                </div>
                <div class="row padding-10">
                    <div class="alert alert-info" ng-show="hasEntries === false">
                        <i class="fa fa-lg fa-info-circle"></i>
                        <?php echo __('No entries match the selection'); ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 margin-top-10 margin-bottom-10">
                <div class="well formactions ">
                    <div class="pull-right">
                        <button type="button" ng-click="createDowntimeReport()" class="btn btn-primary">
                            <?php echo __('Create report'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
