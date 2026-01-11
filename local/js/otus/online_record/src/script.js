(function() {
    'use strict';

    window.Otus = window.Otus || {};
    window.Otus.UserType = window.Otus.UserType || {};

    // Регистрируем в Bitrix
    BX.namespace('Otus.UserType');

    Otus.UserType.OnlineRecord = {
        init: function() {
            // Используем делегирование
            BX.bindDelegate(document.body, 'click', { className: 'book_procedure' }, (e) => {
                this.onAddOnlineRecord(e);
            });
        },

        onAddOnlineRecord: function(e) {
            e.preventDefault();
            const target = e.target;
            const prId = target.getAttribute('data-pr-id');
            const doctorId = target.getAttribute('data-doctor-id');

            const content = BX.create('div', {
                props: { className: 'p-4' },
                children: [
                    BX.create('input', {
                        props: { id: 'name_' + prId, className: 'bx-auth-input', placeholder: 'Ваше ФИО' },
                        style: { width: '100%', marginBottom: '10px', padding: '10px', boxSizing: 'border-box'}
                    }),
                    BX.create('input', {
                        props: { id: 'date_' + prId, type: 'datetime-local', className: 'bx-auth-input' },
                        style: { width: '100%', padding: '10px' , boxSizing: 'border-box'}
                    })
                ]
            });

            // Используем стандартный PopupWindow для лучшей совместимости
            const popup = BX.PopupWindowManager.create('booking_' + prId + '_' + Math.random(), null, {
                content: content,
                titleBar: 'Запись на процедуру',
                closeIcon: {right: '20px', top: '10px'},
                width: 400,
                height: 400,
                zIndex: 100,
                closeByEsc: true,
                darkMode: false,
                autoHide: false,
                draggable: true,
                resizable: true,
                min_height: 100,
                min_width: 100,
                lightShadow: false,
                angle: false,
                overlay: { backgroundColor: 'black', opacity: 500 },
                buttons: [

                    new BX.UI.Button({
                        text: 'Записаться',
                        color: BX.UI.Button.Color.SUCCESS,
                        onclick: (button) => {
                            const name = BX('name_' + prId).value;
                            const date = BX('date_' + prId).value;

                            BX.ajax.runComponentAction("custom:record_online.controller", "addRecord", {
                                mode: "class",
                                data: {
                                    "NAME": name,
                                    "TIME": date,
                                    "PROC_ID": prId,
                                    "DOCTOR_ID": doctorId
                                }
                            }).then(function (response) {
                                popup.close();
                                alert('Заявка принята!');
                            });

                        }
                    })
                ]
            });

            popup.show();
        }
    };

    BX.ready(() => {
        if (Otus.UserType.OnlineRecord) {
            Otus.UserType.OnlineRecord.init();
        }
    });
})();