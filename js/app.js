
/*global browser: true, plusplus: true,Backbone,$,console: false,Mustache */
var testApp = {};
(function () {
    "use strict";
    var ns = testApp,
        properties,
        router,
        propertyListView,
        propertyView,
        Property = Backbone.Model.extend({
            defaults: {
                //id: "",
                address: "",
                property_type: "",
                bedrooms: "",
                bathrooms: "",
                car_spaces: "",
                rent: ""
            },
            url : function () {
                // NOTE!!! The following needs to be configured to match your setup:
                //return (this.isNew() ? "/content/projects/restler-backbone-example/api/property" : "/content/projects/restler-backbone-example/api/property/" + this.id);
                return (this.isNew() ? "/restler-backbone-example/api/property" : "/restler-backbone-example/api/property/" + this.id);
            }
        }),
        PropertyListItemView = Backbone.View.extend({
            tagName:  "li",
            template: $("#property-list-item-template"),
            events: {
                // removed the "blur .edit" event handling as this was overriding the property-edit-cancel click handler 
                //"blur .edit":                 "update",
                "click #property-delete":       "destroy",
                "click #property-edit":         "edit",
                "click #property-edit-cancel":  "cancel",
                "click #property-update":       "update",
                "keypress .edit":               "updateOnEnter"
            },
            initialize: function () {
                this.model.bind("change", this.render, this);
                this.render();
            },
            destroy: function () {
                var self = this;
                this.model.destroy({
                    success: function () {
                        self.remove();
                        $("#notice").html("Property deleted.");
                    },
                    error: function () {
                        $("#notice").html("There was a problem deleting the property.");
                    }
                });
            },
            // Switch this view into "editing" mode, displaying the input field.
            edit: function () {
                // Clear any status mesages
                $("#notice").empty();
                this.backup = this.model.clone();
                // Go into edit mode. This will make the input fields visible
                $(this.el).addClass("editing");
                this.$('#property-list-item-address').focus();
            },
            // Close the "editing" mode, saving changes to the property.
            update: function () {
                var data = {
                    address: this.$('#property-list-item-address').val(),
                    property_type: this.$('#property-list-item-property-type').val(),
                    bedrooms: this.$('#property-list-item-bedrooms').val(),
                    bathrooms: this.$('#property-list-item-bathrooms').val(),
                    car_spaces: this.$('#property-list-item-car-spaces').val(),
                    rent: this.$('#property-list-item-rent').val()
                },
                    self = this;
                this.model.save(data, {
                    success: function () {
                        $("#notice").html("Property updated.");
                        // Go back to view mode
                        $(self.el).removeClass("editing");
                    },
                    error: function (collection, xhr, options) {
                        var errors = JSON.parse(xhr.responseText || "{}").data,
                            errorString = "",
                            item;
                        for (item in errors) {
                            if (errors.hasOwnProperty(item)) {
                                errorString += errors[item] + "<br />";
                            }
                        }
                        $("#notice").html("Errors prevented the property from being updated:<br />" + errorString);
                    }
                });
            },
            cancel: function () {
                // Clear any status mesages
                $("#notice").empty();
                // Go back to view mode
                $(this.el).removeClass("editing");
                this.model = this.backup;
                //the following is necessary otherwise the discarded updates will be viewable the next time
                this.render();
            },
            // If you hit "enter", we're through editing the item.
            updateOnEnter: function (e) {
                if (e.keyCode === 13) {
                    this.update();
                }
            },
            render: function () {
                var html = Mustache.to_html(this.template.html(), this.model.toJSON());
                $(this.el).html(html);
                return this;
            },
            backup: null
        }),
        PropertyListView = Backbone.View.extend({
            el: $("#property-list"),
            initialize: function () {
                // Ghost propertyViews were appearing after cancelling the new user form. This was fixed with the following:
                // (https://paydirtapp.com/blog/backbone-in-practice-memory-management-and-event-bindings/)
                this.collection.unbind();
                $(this.el).empty();
                this.collection.bind("add", this.renderProperty, this);
                this.render();
            },
            renderProperty: function (property) {
                var propertyView = new PropertyListItemView({model: property});
                $(this.el).append(propertyView.render().el);
            },
            render: function () {
                if (this.collection.length > 0) {
                    this.collection.each(this.renderProperty, this);
                } else {
                    $("#notice").html("There are no properties to display.");
                }
            }
        }),
        PropertiesCollection = Backbone.Collection.extend({
            model: Property,
            // NOTE!!! The following needs to be configured to match your setup:
            //url: '/content/projects/restler-backbone-example/api/property',
            url: '/restler-backbone-example/api/property',
            load: function () {
                this.fetch({
                    success: function () {
                        propertyListView = new PropertyListView({ collection: properties });
                    },
                    error: function () {
                        $("#notice").html("Could not load the properties.");
                    }
                });
            }
        }),
        PropertyView = Backbone.View.extend({
            el: $("#property"),
            template: $("#property-template"),
            events: {
                "click #property-add-cancel": "cancel",
                "click #property-add": "add",
                "click #property-save": "save"
            },
            initialize: function () {
                this.render();
            },
            add: function () {
                $("#notice").empty();
                // Update the URL (trigger=false tells Backbone to only change the URL in the address bar, not perform a full reload
                // via Backbone.Router)
                router.navigate("#new", { trigger: false });
                //the following is necessary to clear out any previous input
                this.model = new Property();
                this.render();
                // Go into edit mode. This will make the input fields visible
                $(this.el).addClass("editing");
                this.$('#property-address').focus();
            },
            edit: function (id) {
                var self;
                $("#notice").empty();
                this.model = new Property({id: id});
                self = this;
                this.model.fetch({
                    success: function () {
                        self.render();
                        // Go into edit mode. This will make the input fields visible
                        $(self.el).addClass("editing");
                        self.$('#property-address').focus();
                    },
                    error: function () {
                        $("#notice").html("Could not load the property.");
                    }
                });
            },
            cancel: function () {
                // Clear any status mesages
                $("#notice").empty();
                // Update the URL (trigger=false tells Backbone to only change the URL in the address bar, not perform a full reload 
                // via Backbone.Router)
                router.navigate("#", { trigger: false });
                // Go back to view mode
                $(this.el).removeClass("editing");
            },
            close: function () {
                $(this.el).unbind();
                $(this.el).empty();
            },
            save: function () {
                var data = {
                    address: $("#property-address").val(),
                    property_type: $('#property-property-type').val(),
                    bedrooms: $('#property-bedrooms').val(),
                    bathrooms: $('#property-bathrooms').val(),
                    car_spaces: $('#property-car-spaces').val(),
                    rent: $('#property-rent').val()
                },
                    self = this;
                self.wasNew = this.model.isNew();
                this.model.save(data, {
                    success: function () {
                        // If this was a new property then we can simply append it to the local collection.
                        // However for an existing property we need to refresh the collection, so that our updates to be visible
                        if (self.wasNew) {
                            $("#notice").html("Property created.");
                            properties.add(self.model);
                        } else {
                            $("#notice").html("Property updated.");
                            properties.load();
                        }
                        // Update the URL (trigger=false tells Backbone to only change the URL in the address bar, not perform a full reload 
                        // via Backbone.Router)
                        router.navigate("#", {trigger: false});
                        // Go back to view mode
                        $(self.el).removeClass("editing");
                    },
                    error: function (collection, xhr, options) {
                        var errors = JSON.parse(xhr.responseText || "{}").data,
                            errorString = "",
                            item;
                        for (item in errors) {
                            if (errors.hasOwnProperty(item)) {
                                errorString += errors[item] + "<br />";
                            }
                        }
                        if (self.wasNew) {
                            $("#notice").html("Errors prevented the property from being created:<br />" + errorString);
                        } else {
                            $("#notice").html("Errors prevented the property from being updated:<br />" + errorString);
                        }
                    }
                });
            },
            render: function () {
                var html = Mustache.to_html(this.template.html(), this.model.toJSON());
                $(this.el).html(html);
            }
        }),
        AppRouter = Backbone.Router.extend({
            routes: {
                "new":     "newProperty",
                "id=:id":  "getProperty",
                "":        "index"
            },
            newProperty: function () {
                propertyView.add();
            },
            getProperty: function (id) {
                propertyView.edit(id);
            },
            index: function () {
            }
        });

    function init() {
        properties = new PropertiesCollection();
        properties.load();
        propertyView = new PropertyView({model: new Property()});
        $.ajaxSetup({ cache: false });
        router = new AppRouter();
        Backbone.history.start();
    }
    ns.init = init;
}());

$(function () {
    "use strict";
    testApp.init();
});
