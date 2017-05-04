define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	'dojo/_base/array',
	'dojo/on',
	'dojo/aspect',
	'dojo/dom-construct',
	'dojo/query',
    'dojo/topic',
	'dijit/registry',
	'dijit/Toolbar',
	'dijit/ToolbarSeparator',

    "dijit/form/DropDownButton",
    'dijit/PopupMenuBarItem',
    'dijit/DropDownMenu',
    'dijit/MenuItem',
    'dijit/MenuSeparator',
    'dijit/PopupMenuItem',
    'dijit/CheckedMenuItem',

    "cool/cool",
	"cool/dialog/manager",
	"cool/dijit/Button",
	'dijit/form/Select',
	'cool/rfe/config/fileObject',
	'cool/rfe/SearchBox'
], function(lang, declare, array, on, aspect, domConstruct, query, topic, registry, Toolbar, ToolbarSeparator,

            DropDownButton, PopupMenuBarItem, DropDownMenu, MenuItem, MenuSeparator, PopupMenuItem, CheckedMenuItem,

            cool, dialogManager, Button, Select, fileObject, SearchBox) {

	/**
	 * @class cool.rfe.layout.Toolbar
	 * @extends dijit.Toolbar
	 * @property {rfe} rfe reference to remoteFileExplorer
	 */
	return declare([Toolbar], /** @lends rfe.layout.Toolbar.prototype */ {

		rfe: null,

		constructor: function(props) {
			lang.mixin(this, props || {});
		},

		/**
		 * Adds the buttons to the toolbar buttons and defines their actions.
		 */
		postCreate: function() {
			this.inherited('postCreate', arguments);	// in case we've overriden something

			var rfe = this.rfe, bt1, bt2, bt3, bt4, div, selSort;

			bt1 = new Button({
				label: rfe.translator.trans("history_up"),
				showLabel: true,
				iconClass: 'rfeIcon rfeToolbarIconDirUp',
				disabled: true,
				onClick: function() {
					var def = rfe.goDirUp();
					def.then(function(object) {
						if (object) {
							rfe.set('history', rfe.currentTreeObject.id);
						}
					});
				}
			});
			rfe.currentTreeObject.watch('id', function(prop, oldVal, newVal) {
				bt1.set('disabled', newVal === rfe.tree.rootNode.item.id);
			});
			this.addChild(bt1);

			bt2 = new Button({
				label: rfe.translator.trans("history_back"),
				showLabel: false,
				iconClass: 'rfeIcon rfeToolbarIconHistoryBack',
				disabled: true,
				onClick: function() {
					rfe.goHistory('back');
				}
			});
			rfe.watch('history', function() {
				bt2.set('disabled', rfe.history.steps.length < 2);
			});
			aspect.after(rfe, 'goHistory', function() {
				bt2.set('disabled', rfe.history.curIdx < 1);
			});
			this.addChild(bt2);

			bt3 = new Button({
				label: rfe.translator.trans("history_forward"),
				showLabel: false,
				iconClass: 'rfeIcon rfeToolbarIconHistoryForward',
				disabled: true,
				onClick: function() {
					rfe.goHistory('forward');
				}
			});
			aspect.after(rfe, 'goHistory', function() {
				bt3.set('disabled', rfe.history.curIdx > rfe.history.steps.length - 2);
			});
			this.addChild(bt3);

			this.addChild(new Button({
				label: rfe.translator.trans("reload"),
				showLabel: true,
				iconClass: 'rfeIcon rfeToolbarIconReload',
				disabled: false,
				onClick: function() {
					rfe.reload();
				}
			}));
			this.addChild(new ToolbarSeparator({ /*id: 'rfeTbSeparatorSearch'*/ }));

			rfe.searchBox = new SearchBox({
				target: rfe.serviceQueryStringSuggestion,
				rfe: rfe,
				"style": {
					width:"500px"
				}
			});

			this.addChild(rfe.searchBox);

			this.addChild(new Button({
				showLabel: false,
				iconSrc: '/bower_components/fugue/icons/magnifier-left.png',
				disabled: false,
				onClick: function() {
					rfe.search();
				}
			}));

            this.addChild(new ToolbarSeparator({ /*id: 'rfeTbSeparatorSearch2' */}));

            this.addChild(new Button({
                label: rfe.translator.trans("upload"),
                showLabel: true,
                iconSrc: '/bower_components/fugue/icons/upload.png',
                disabled: false,
                onClick: function() {
                    var d = dialogManager.openWidgetDialog('EulogixCoolCore/Core/Files/FileRepositoryUploaderForm', 'UPLOAD', lang.mixin({
                        folder: rfe.currentTreeObject.id
                    },rfe.commonParameters), null, null, null, {
						w: 800,
						h: 500
					});
                    d.rfe = rfe;
                }
            }));

			this.addChild(new ToolbarSeparator({ /*id: 'rfeTbSeparatorSearch2' */}));

            var submenuView = {};

            // ******* menu layout ********
            menuView = new DropDownMenu();
            submenuView.icons = new MenuItem({
                label: rfe.translator.trans("layout_icons"),
                checked: false,
                onClick: lang.hitch(this, function() {
                    topic.publish('grid/views/state', 'icons');
                }),
                iconClass: 'rfeIcon rfeMenuIconThumbs'
            });
            submenuView.list = new MenuItem({
                label: rfe.translator.trans("layout_list"),
                checked: false,
                onClick: lang.hitch(this, function() {
                    topic.publish('grid/views/state', 'list');
                }),
                iconClass: 'rfeIcon rfeMenuIconList'
            });
            menuView.addChild(submenuView.icons);
            menuView.addChild(submenuView.list);


            var button = new DropDownButton({
                optionsTitle: rfe.translator.trans("options_view"),
                label: rfe.translator.trans("options_view"),
                dropDown: menuView,'class': 'rfeToolbarSort'
            });
            this.addChild(button);


			div = domConstruct.create('div', {
				'class': 'rfeToolbarSort'
			}, this.domNode);
			domConstruct.create('label', {
				innerHTML: rfe.translator.trans("sort_by")
			}, div);

			var options = array.map(fileObject.sortOptions, function(prop) {
				return {
                    label: rfe.translator.trans("sort_"+fileObject.label[prop]),
                    value: prop
                }
			});
			selSort = new Select({
				options: options
			}).placeAt(div);

			bt4 = new Button({
				label: rfe.translator.trans("sort"),
				showLabel: false,
				iconClass: 'rfeIcon rfeToolbarIconSortAsc',
				onClick: function () {
					var node, field = selSort.get('value');
					if (rfe.grid.view === 'icons') {
						var sortObj = rfe.grid._sort[0];
						rfe.grid.set('sort', [{ attribute : field, descending : (sortObj && !sortObj.descending) }]);
					}
					else {
						// simulate clicking on grid column
						node = query('th.field-' + field)[0];
						on.emit(node, 'click', {
							cancelable: true,
							bubbles: true
						});
					}
				}
			}).placeAt(div);

			// sync grid header column and sort button
			var signal = aspect.after(rfe, 'initGrid', function() {
				signal.remove();
				// grid not initialized yet, so we can't do this directly
				aspect.after(rfe.grid, '_setSort', function(arrSort) {
					if (arrSort && arrSort.length > 0) {	// set sort is also called by set('query') which doesn't mean user clicked sorting -> arrSort is undefined
						var sortObj = arrSort[1] || arrSort[0];

						bt4.set('iconClass', 'rfeIcon rfeToolbarIconSort' + (sortObj.descending ? 'Desc' : 'Asc'));
						selSort.set('value', sortObj.attribute);
					}
				}, true);
			});
		},

		_onContainerKeydown: function(evt) {
			var widget = registry.getEnclosingWidget(evt.target);
			if (!widget.textbox) {
				this.inherited('_onContainerKeydown', arguments);
			}
		},

		_onContainerKeypress: function(evt) {
			var widget = registry.getEnclosingWidget(evt.target);
			if (!widget.textbox) {
				this.inherited('_onContainerKeydown', arguments);
			}
		}
	});
});