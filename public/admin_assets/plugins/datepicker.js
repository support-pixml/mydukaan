jQuery(document).ready(function($) {
    'use strict';

    if ($("#datetimepicker1").length) {
        $('#datetimepicker1').datetimepicker();

    }

    /* Calender jQuery **/

    if ($("datetimepicker2").length) {

        $('#datetimepicker2').datetimepicker({
            locale: 'ru',
            icons: {
                time: "far fa-clock",
                date: "fa fa-calendar-alt",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
    }


    if ($("#datetimepicker3").length) {

        $('#datetimepicker3').datetimepicker({
            format: 'LT'
        });
    }

    if ($("#start_time").length) {

        $('#start_time').datetimepicker({
            format: 'HH:mm',
            // format: 'LT'
        });
    }

    if ($("#end_time").length) {

        $('#end_time').datetimepicker({
            // format: 'LT'
            format: 'HH:mm',
        });
    }

    if ($("#edit_start_time").length) {

        $('#edit_start_time').datetimepicker({
            format: 'LT'
        });
    }

    if ($("#edit_end_time").length) {

        $('#edit_end_time').datetimepicker({
            format: 'LT'
        });
    }

    if ($("#datetimepicker4").length) {
        $('#datetimepicker4').datetimepicker({
            format: 'YYYY-MM-DD'
        });

    }
    if ($("#datetimepicker5").length) {
        $('#datetimepicker5').datetimepicker();

    }

    if ($("#datetimepicker6").length) {
        $('#datetimepicker6').datetimepicker({
            defaultDate: "11/1/2013",
            disabledDates: [
                moment("12/25/2013"),
                new Date(2013, 11 - 1, 21),
                "11/22/2013 00:53"
            ],
            icons: {
                time: "far fa-clock",
                date: "fa fa-calendar-alt",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
    }

    if ($("#datetimepicker7").length) {
        $(function() {
            $('#datetimepicker7').datetimepicker({                
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                }
            });
            $('#datetimepicker8').datetimepicker({                
                useCurrent: false,
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                }
            });
            $("#datetimepicker7").on("change.datetimepicker", function(e) {
                $('#datetimepicker8').datetimepicker('minDate', e.date);
            });
            $("#datetimepicker8").on("change.datetimepicker", function(e) {
                $('#datetimepicker7').datetimepicker('maxDate', e.date);
            });
        });
    }

    if ($("#start_date").length) {
        $(function() {
            $('#start_date').datetimepicker({
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                },
                format: 'L'
            });
            $('#end_date').datetimepicker({
                useCurrent: false,
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                },
                format: 'L'
            });
            $("#start_date").on("change.datetimepicker", function(e) {
                $('#end_date').datetimepicker('minDate', e.date);
            });
            $("#end_date").on("change.datetimepicker", function(e) {
                $('#start_date').datetimepicker('maxDate', e.date);
            });
        });
    }

    if ($("#edit_start_date").length) {
        $(function() {
            $('#edit_start_date').datetimepicker({
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                },
                format: 'L'
            });
            $('#edit_end_date').datetimepicker({
                useCurrent: false,
                icons: {
                    time: "far fa-clock",
                    date: "fa fa-calendar-alt",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                },
                format: 'L'
            });
            $("#edit_start_date").on("change.datetimepicker", function(e) {
                $('#edit_end_date').datetimepicker('minDate', e.date);
            });
            $("#edit_end_date").on("change.datetimepicker", function(e) {
                $('#edit_start_date').datetimepicker('maxDate', e.date);
            });
        });
    }

    if ($("#datetimepicker10").length) {
        $('#datetimepicker10').datetimepicker({
            viewMode: 'years',
            icons: {
                time: "far fa-clock",
                date: "fa fa-calendar-alt",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
    }

    if ($("#datetimepicker11").length) {
        $('#datetimepicker11').datetimepicker({
            viewMode: 'years',
            format: 'MM/YYYY'
        });
    }

if ($("#datetimepicker13").length) {
     $('#datetimepicker13').datetimepicker({
            inline: true,
            sideBySide: true
        });

}
});