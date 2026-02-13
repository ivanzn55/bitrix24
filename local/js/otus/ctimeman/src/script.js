BX.ready(function () {

    'use strict';

    //onAjaxSuccessFinish

    //onPullEvent-timeman


    const Messages = {
        OPEN_DAY_TITLE: BX.message('OTUS_TIMEMAN_OPEN_DAY_TITLE') || 'Начать рабочий день?',
        OPEN_DAY_MESSAGE: BX.message('OTUS_TIMEMAN_OPEN_DAY_MESSAGE') || 'Вы действительно хотите начать рабочий день?',
        REOPEN_DAY_TITLE: BX.message('OTUS_TIMEMAN_REOPEN_DAY_TITLE') || 'Продолжить рабочий день?',
        REOPEN_DAY_MESSAGE: BX.message('OTUS_TIMEMAN_REOPEN_DAY_MESSAGE') || 'Вы действительно хотите продолжить рабочий день?',
        CLOSE_DAY_TITLE: BX.message('OTUS_TIMEMAN_CLOSE_DAY_TITLE') || 'Завершить рабочий день?',
        CLOSE_DAY_MESSAGE: BX.message('OTUS_TIMEMAN_CLOSE_DAY_MESSAGE') || 'Вы действительно хотите завершить рабочий день?',
        BUTTON_YES: BX.message('OTUS_TIMEMAN_BUTTON_YES') || 'Да',
        BUTTON_CANCEL: BX.message('OTUS_TIMEMAN_BUTTON_CANCEL') || 'Отмена'
    };

    let status = '';
    let confirmationPopup = null;


    function showConfirmationDialog(title, message, onConfirm, onCancel) {

        try {

            confirmationPopup = new BX.PopupWindow("timeManConfirmation", null, {
                content: message,
                closeIcon: {right: "20px", top: "10px"},
                titleBar: {content: BX.create("span", {html: title, 'props': {'className': 'otus-time-man-confirmation_title'}})},
                zIndex: 0,
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: false},
                overlay: {
                    backgroundColor: 'black',
                    opacity: 50
                },
                closeByEsc: true,
                className: 'otus-time-man-confirmation',
                buttons: [
                    new BX.PopupWindowButton({
                        text: Messages.BUTTON_YES,
                        className: "popup-window-button-accept",
                        events: {
                            click: function(){

                                if (typeof onConfirm === 'function') {
                                    onConfirm();
                                }

                                //button.click();
                                this.popupWindow.close();
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: Messages.BUTTON_CANCEL,
                        className: "webform-button-link-cancel",
                        events: {
                            click: function(){

                                this.popupWindow.close();

                                if (typeof onCancel === 'function') {
                                    onCancel();
                                }
                            }
                        }
                    })
                ]
            });

            confirmationPopup.show();

        } catch (error) {


        }

    }


    BX.addCustomEvent('onTimeManDataRecieved', function(e) {
        status = e.STATE;
        //PAUSED
        //CLOSED
    });


    BX.addCustomEvent('onTaskTimerChange', function() {


        if(status === 'PAUSED' || status === 'CLOSED'){

            setTimeout(function () {


                const button = document.querySelector('#popup-window-content-bx-avatar-header-popup .tm-control-panel .tm-control-panel__actions-item button');


                const copyButton = button.cloneNode(true);
                const parent = button.parentNode;

                button.style.display = 'none';
                parent.appendChild(copyButton);

                let title = status === 'CLOSED' ? Messages.OPEN_DAY_TITLE : Messages.REOPEN_DAY_TITLE;
                let message = status === 'CLOSED' ? Messages.OPEN_DAY_MESSAGE : Messages.REOPEN_DAY_MESSAGE;

                copyButton.addEventListener('click', function(e){
                    e.preventDefault();
                    showConfirmationDialog(
                        title,
                        message,
                        function(){
                            button.click();
                        },
                        false
                    )
                });


            }, 200);

        }



        /*button.addEventListener('click', function (e){
                e.preventDefault();
                e.stopPropagation();
                alert(21212);
            });*/



    });



    BX.addCustomEvent('onPullEvent-timeman', function(e) {

        console.log('event: ');
        console.log(e);

        if(e ===  'pause'){
            const button = document.querySelector('#popup-window-content-bx-avatar-header-popup .tm-control-panel .tm-control-panel__actions-item button');


            const copyButton = button.cloneNode(true);
            const parent = button.parentNode;

            button.style.display = 'none';
            parent.appendChild(copyButton);

            let title = Messages.REOPEN_DAY_TITLE;
            let message = Messages.REOPEN_DAY_MESSAGE;

            copyButton.addEventListener('click', function(e){
                e.preventDefault();
                showConfirmationDialog(
                    title,
                    message,
                    function(){
                        button.click();
                    },
                    false
                )
            });
        }


    })


});