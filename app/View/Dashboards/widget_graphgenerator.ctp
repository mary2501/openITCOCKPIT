<header dashboard-widget-header-directive=""
        class="ui-draggable-handle pointer"
        wtitle="title"
        wid="id"
        update-title="updateTitle({id:id,title:title})">
</header>

<div class="content" style="">

    <!-- widget edit box -->
    <div class="jarviswidget-editbox not-draggable" style="display: none;">
        <!-- This area used as dropdown edit box -->
        <input class="form-control" type="text" placeholder="Widget title" ng-model="title"
               ng-model-options="{debounce: 1000}">
        <span class="note"><i class="fa fa-check text-success"></i>
            <?php echo __('Change title to update and save instantly'); ?>
        </span>
        <hr>
        <div class="col col-xs-12" ng-show="widget.id">
            <select id="map-{{id}}"
                    data-placeholder="<?php echo __('Please select...'); ?>"
                    class="form-control"
                    chosen="services"
                    ng-options="graph.GraphgenTmpl.id as graph.GraphgenTmpl.name for graph in all_templates"
                    ng-model="widget.id">
            </select>
        </div>
    </div>

    <div class="widget-body padding-0 not-draggable">
        <div class="col col-xs-12" ng-show="(!widget.id && !error) || error == 'Invalid graph'">
            <select id="map-{{id}}"
                    data-placeholder="<?php echo __('Please select...'); ?>"
                    class="form-control"
                    chosen="services"
                    ng-options="graph.GraphgenTmpl.id as graph.GraphgenTmpl.name for graph in all_templates"
                    ng-model="widget.id">
            </select>
        </div>
        <div class="col col-xs-12" ng-show="error">
            <p ng-bind="error" class="font-lg text-danger margin-top-10"></p>
        </div>
        <div id="graphCanvas" class="col col-xs-12" ng-show="!error" style="padding: 0px; position: relative;"></div>
    </div>
</div>
