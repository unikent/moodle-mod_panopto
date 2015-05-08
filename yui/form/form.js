/**
 * This handles Panopto embed.
 * 
 * @package    mod_panopto
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

YUI.add('moodle-mod_panopto-form', function(Y) {
    M.mod_panopto = M.mod_panopto || {};
    M.mod_panopto.form = M.mod_panopto.form || {
		init: function(servername, coursefolder) {
            this.servername = servername;
            this.coursefolder = coursefolder;

            var button = Y.Node.create("<button>Select Video</button>");
            button.on('click', this.handle_button_click, this);

            Y.one('#fitem_id_externalid .felement').append(button);
		},

		handle_button_click: function(e) {
			e.preventDefault();

			var modal = this.create_modal();
			this.handle_postback(modal);
		},

		create_modal: function() {
			var url = 'https://' + this.servername + '/Panopto/Pages/Sessions/EmbeddedUpload.aspx';
			if (this.coursefolder) {
				url = url + '?folderId=' + this.coursefolder;
			}

			// Create a modal box.
		    var modal = Y.Node.create("<div id=\"panopto-modal-overlay\"></div>");
		    var modalcontent = Y.Node.create("<div id=\"panopto-modal\"></div>");
		    var modalclose = Y.Node.create("<button id=\"panopto-modal-close\">Close Window</button>");
		    modalclose.on('click', this.handle_modal_close, this, modal);

		    var iframe = Y.Node.create("<iframe id=\"panopto-iframe\" src=\"" + url + "\"></iframe>");

		    modalcontent.append(iframe);
		    modalcontent.append(modalclose);
		    modal.append(modalcontent);

            Y.one('body').append(modal);

			var x = (window.innerWidth - parseInt(modalcontent.getComputedStyle("width"))) / 2;
			modalcontent.setX(x);

			return modal;
		},

		handle_postback: function(modal) {
	        // Setup event handling for the iframe.
	        var parent = this;
			var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	        var eventEnter = window[eventMethod];
	        var messageEvent = eventMethod === "attachEvent" ? "onmessage" : "message";

	        eventEnter(messageEvent, function (e) {
	            var message = JSON.parse(e.data);

	            if (message.cmd === 'notReady') {
	                return;
	            }

	            if (message.cmd === 'ready') {
					// Ask for more data.
					var win = document.getElementById("panopto-iframe").contentWindow,
					message = { cmd: "createEmbeddedFrame" };
					win.postMessage(JSON.stringify(message), 'https://' + parent.servername);
					modal.hide();
	            }

	            if (message.cmd === 'deliveryList') {
	                ids = message.ids;

	                for (var value in ids) {
	                	parent.handle_id(ids[value]);
	                }

	                modal.hide();
	            }
	        }, false);
		},

		handle_id: function(videoid) {
			var elem = Y.one('#id_externalid');
			elem.set('value', videoid);
		},

		handle_modal_close: function(e, modal) {
			e.preventDefault();

			modal.hide();
		}
	};
}, '@VERSION@', {requires: ['node', 'event', 'json'] });
