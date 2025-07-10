define([
    'jquery',
    'underscore',
    'mage/storage',
    'Magento_Ui/js/lib/spinner',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'jquery-ui-modules/widget',
    'jsbasetree'
], function ($, _, storage, loader, alert, $t, confirm) {
    'use strict';

    $.widget('mage.caUnitTree', {
        options: {
            initSelector: '.tree-init',
            buttonWrapper: $('.aw-ca-customer-unit-btn-wrapper'),
            expandButtonSelector: '.expand-tree-button',
            collapseButtonSelector: '.collapse-tree-button',
            addNewButtonSelector: '.action-add-new-unit',
            editButtonSelector: '.action-edit-unit',
            deleteButtonSelector: '.action-delete-unit',
            formLoaderId: 'aw_ca_unit_form.aw_ca_unit_form',
            moveUrl: '',
            isAdmin: false,
            rootUnitId: '',
            deleteUrl: '',
            units: [],
            checkCallback: true,
            treeConfig: {
                plugins: ['dnd', 'conditionalselect'],
                core: {
                    data: [],
                    check_callback: true
                },
                dnd: {
                    drag_selection: false,
                    copy: false
                }
            }
        },

        /**
         * Create function to prepare tree config
         */
        _create: function () {
            this._prepareTreeConfig();
            this._bind();
            this.element.find(this.options.initSelector).jstree4(this.options.treeConfig);
        },

        /**
         * Prepare tree config
         *
         * @private
         */
        _prepareTreeConfig: function () {
            this._formatUnits();
            this.options.treeConfig.core.data = this.options.units;
            this.options.treeConfig.core.check_callback = this.options.checkCallback;
            this.options.treeConfig.conditionalselect = $.proxy(this._redirectToEdit, this);
        },

        /**
         * Format units
         *
         * @private
         */
        _formatUnits: function () {
            var i;
            for (i = 0; i < this.options.units.length; i++) {
                this.options.units[i]['text'] = this._escapeHtml(this.options.units[i]['text']);
            }
        },

        /**
         * Escape html tags
         *
         * @param text
         * @returns string
         * @private
         */
        _escapeHtml: function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.replace(/[&<>"']/g, function (m) { return map[m]; });
        },

        /**
         * Bind callbacks
         *
         * @private
         */
        _bind: function () {
            var tree = this.element.find(this.options.initSelector),
                collapseButton = this.element.find(this.options.collapseButtonSelector),
                expandButton = this.element.find(this.options.expandButtonSelector);
            var addNewButton = this.options.buttonWrapper.find(this.options.addNewButtonSelector),
                editButton = this.options.buttonWrapper.find(this.options.editButtonSelector),
                deleteButton = this.options.buttonWrapper.find(this.options.deleteButtonSelector);

            if (tree.length) {
                var self = this;
                tree.on('move_node.jstree4', $.proxy(this._moveNode, this));
                tree.on('select_node.jstree4', function (event, node) {
                    var selected = node.instance.get_selected();
                    self.element.parents('.chooser_container').find('.input-text.entities').val(selected.join());
                });

                tree.on('deselect_node.jstree4', function (event, node) {
                    var selected = node.instance.get_selected();
                    self.element.parents('.chooser_container').find('.input-text.entities').val(selected.join());
                });
                if (collapseButton.length) {
                    collapseButton.on('click', function () {
                        tree.jstree4('close_all');
                    });
                }
                if (expandButton.length) {
                    expandButton.on('click', function () {
                        tree.jstree4('open_all');
                    });
                }
                if (addNewButton.length) {
                    addNewButton.on('click', function () {
                        var selectedNode = ref.element.find(".jstree4-clicked");
                        var newunitUrl = $(this).attr("data-href");
                        if (selectedNode.length) {
                            if (newunitUrl.endsWith('/')) {
                                newunitUrl = newunitUrl.slice(0, -1);
                            }
                            newunitUrl = newunitUrl + "/parent/" + $(selectedNode).parent().attr("id");
                        }
                        window.open(newunitUrl, '_self');
                    });
                }
                var ref = this;
                if (editButton.length) {
                    editButton.on('click', function () {
                        ref.editTreeNode();
                    });
                }
                if (deleteButton.length) {
                    deleteButton.on('click', function () {
                        confirm({
                            content: $t('Do you want to delete this unit? Related sub-units will be deleted.'),
                            actions: {
                                confirm: function () {
                                    ref.deleteTreeNode(tree);
                                }
                            }
                        });
                    });
                }
            }
        },

        /**
         * Edit Tree Node
         */
        editTreeNode: function () {
            var selectedNode = this.element.find(".jstree4-clicked");
            if (selectedNode.length) {
                window.open($(selectedNode).attr("href"), '_self');
            }
            else {
                alert({
                    title: $t('Edit Unit'),
                    content: $t('Please select unit from tree.')
                });
            }
        },

        /**
         * Delete Tree Node
         */
        deleteTreeNode: function (tree) {
            var selectedNode = this.element.find(".jstree4-clicked"),
                self = this;
                var elm = tree.jstree4("refresh");
               
            if (selectedNode.length) {
                var nodeId = selectedNode.parent().attr("id");
                this.showLoader();
                $.ajax({
                    url: this.options.deleteUrl,
                    type: 'POST',
                    form_key: this.getFormKey(),
                    data: { form_key: this.getFormKey(), unitId: nodeId },
                    dataType: 'json',
                    showLoader: true
                }).done(
                    function (response) {
                        if (!response.success) {
                            self.showErrorAlert(response.message);
                        }
                        else {
                            selectedNode.parent().remove();
                            setTimeout(() => {
                                window.location.reload();
                              }, "2000");
                        }
                    }
                ).fail(
                    function () {
                        self.showErrorAlert($t('Something went wrong while moving the unit.'));
                    }
                ).always(
                    function () {
                        self.hideLoader();
                    }
                );
            }
            else {
                alert({
                    title: $t('Delete Unit'),
                    content: $t('Please select unit from tree.')
                });
            }
        },
        /**
         * Move node
         *
         * @param {Event} event
         * @param {Object} data
         * @private
         */
        _moveNode: function (event, data) {
            var tree = this.element.find(this.options.initSelector),
                node = data.node,
                self = this;

            if (node) {
                this.showLoader();
                storage.post(
                    this.options.moveUrl,
                    {
                        form_key: this.getFormKey(),
                        nodes_data: this._getNodesPositionData(node)
                    },
                    true,
                    'application/x-www-form-urlencoded; charset=UTF-8'
                ).done(
                    function (response) {
                        if (!response.success) {
                            self.showErrorAlert(response.message);
                        }
                    }
                ).fail(
                    function () {
                        self.showErrorAlert($t('Something went wrong while moving the unit.'));
                    }
                ).always(
                    function () {
                        self.hideLoader();
                    }
                );
            }
        },

        /**
         * Retrieve nodes position data
         *
         * @param {Object} currentNode
         * @return {Array}
         * @private
         */
        _getNodesPositionData: function (currentNode) {
            var tree = this.element.find(this.options.initSelector),
                prevNode = tree.jstree4('get_node', tree.jstree4('get_prev_dom', currentNode, true)),
                parentNode = currentNode.parent !== '#' ? tree.jstree4('get_node', currentNode.parent) : false,
                sortOrderStart = parentNode ? parseInt(parentNode.data.sort_order) : 0,
                node = currentNode,
                data = [],
                firstChildNode;

            if (!prevNode) {
                sortOrderStart = sortOrderStart + 1000;
                prevNode = { data: { sort_order: sortOrderStart } };
            }

            while (node && node.id) {
                node.data.sort_order = parseInt(prevNode.data.sort_order) + 10;
                data.push({
                    target_id: node.id,
                    parent_id: node.parent,
                    path: this._getNodePath(node),
                    sort_order: node.data.sort_order
                });
                if (!_.isEmpty(node.children)) {
                    firstChildNode = tree.jstree4('get_node', _.first(node.children));
                    data = _.union(data, this._getNodesPositionData(firstChildNode));
                }
                prevNode = node;
                node = tree.jstree4('get_node', tree.jstree4('get_next_dom', node, true));
            }

            return data;
        },

        /**
         * Retrieve node path
         *
         * @param {Object} node
         * @return string
         * @private
         */
        _getNodePath: function (node) {
            var path = node.id;

            _.each(node.parents, function (parentId) {
                if (parentId !== '#') {
                    path = parentId + '/' + path;
                }
            });

            return path;
        },

        /**
         * Redirect to edit
         *
         * @param {Object} node
         * @return {Boolean}
         * @private
         */
        _redirectToEdit: function (node) {
            var deleteButton = this.options.buttonWrapper.find(this.options.deleteButtonSelector);
            if (this.options.isAdmin) {
                window.open(node.a_attr.href, '_self');
            }
            else {
                var selectedElement = this.element.find($("[href='" + node.a_attr.href + "']"));
                this.element.find(".jstree4-anchor").removeClass("jstree4-clicked");
                selectedElement.addClass("jstree4-clicked");
                if (this.options.rootUnitId == selectedElement.parent().attr("id")) {
                    deleteButton.hide();
                }
                else {
                    deleteButton.show();
                }
            }
            return false
        },

        /**
         * Retrieve form key
         *
         * @returns {String}
         */
        getFormKey: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return window.FORM_KEY;
        },

        /**
         * Hides loader.
         */
        hideLoader: function () {
            if ($('[data-component="' + this.options.formLoaderId + '"]').length) {
                loader.get(this.options.formLoaderId).hide();
            }
            else {
                $('body').trigger('processStop');
            }
        },

        /**
         * Shows loader.
         */
        showLoader: function () {
            if ($('[data-component="' + this.options.formLoaderId + '"]').length) {
                loader.get(this.options.formLoaderId).show();
            }
            else {
                $('body').trigger('processStart');
            }
        },

        /**
         * Show error alert
         *
         * @param {String} content
         */
        showErrorAlert: function (content) {
            alert({
                title: $t('Error'),
                content: content,
                actions: {
                    always: function () {
                        window.location.reload();
                    }
                }
            });
        }
    });

    return $.mage.caUnitTree;
});
