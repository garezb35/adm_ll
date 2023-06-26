<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8" />
    <title> @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">

    @include('layouts.head-css')
    @include('layouts.vendor-scripts')
</head>

@yield('body')

@yield('content')
<script>
    /**
     * Choices Select plugin
     */
    var choicesExamples = document.querySelectorAll("[data-choices]");
    Array.from(choicesExamples).forEach(function (item) {
        var choiceData = {};
        var isChoicesVal = item.attributes;
        if (isChoicesVal["data-choices-groups"])
            choiceData.placeholderValue =
                "This is a placeholder set in the config";
        if (isChoicesVal["data-choices-search-false"])
            choiceData.searchEnabled = false;
        if (isChoicesVal["data-choices-search-true"])
            choiceData.searchEnabled = true;
        if (isChoicesVal["data-choices-removeItem"])
            choiceData.removeItemButton = true;
        if (isChoicesVal["data-choices-sorting-false"])
            choiceData.shouldSort = false;
        if (isChoicesVal["data-choices-sorting-true"])
            choiceData.shouldSort = true;
        if (isChoicesVal["data-choices-multiple-remove"])
            choiceData.removeItemButton = true;
        if (isChoicesVal["data-choices-limit"])
            choiceData.maxItemCount =
                isChoicesVal["data-choices-limit"].value.toString();
        if (isChoicesVal["data-choices-limit"])
            choiceData.maxItemCount =
                isChoicesVal["data-choices-limit"].value.toString();
        if (isChoicesVal["data-choices-editItem-true"])
            choiceData.maxItemCount = true;
        if (isChoicesVal["data-choices-editItem-false"])
            choiceData.maxItemCount = false;
        if (isChoicesVal["data-choices-text-unique-true"])
            choiceData.duplicateItemsAllowed = false;
        if (isChoicesVal["data-choices-text-disabled-true"])
            choiceData.addItems = false;
        isChoicesVal["data-choices-text-disabled-true"]
            ? new Choices(item, choiceData).disable()
            : new Choices(item, choiceData);
    });

    /**
     * flatpickr
     */
    var flatpickrExamples = document.querySelectorAll("[data-provider]");
    Array.from(flatpickrExamples).forEach(function (item) {
        if (item.getAttribute("data-provider") == "flatpickr") {
            var dateData = {};
            var isFlatpickerVal = item.attributes;
            if (isFlatpickerVal["data-date-format"])
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            if (isFlatpickerVal["data-enable-time"]) {
                (dateData.enableTime = true),
                    (dateData.dateFormat =
                        isFlatpickerVal[
                            "data-date-format"
                            ].value.toString() + " H:i");
            }
            if (isFlatpickerVal["data-altFormat"]) {
                (dateData.altInput = true),
                    (dateData.altFormat =
                        isFlatpickerVal["data-altFormat"].value.toString());
            }
            if (isFlatpickerVal["data-minDate"]) {
                dateData.minDate =
                    isFlatpickerVal["data-minDate"].value.toString();
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-maxDate"]) {
                dateData.maxDate =
                    isFlatpickerVal["data-maxDate"].value.toString();
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-default-date"]) {
                dateData.defaultDate =
                    isFlatpickerVal["data-default-date"].value.toString();
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-multiple-date"]) {
                dateData.mode = "multiple";
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-range-date"]) {
                dateData.mode = "range";
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-inline-date"]) {
                (dateData.inline = true),
                    (dateData.defaultDate =
                        isFlatpickerVal[
                            "data-default-date"
                            ].value.toString());
                dateData.dateFormat =
                    isFlatpickerVal["data-date-format"].value.toString();
            }
            if (isFlatpickerVal["data-disable-date"]) {
                var dates = [];
                dates.push(isFlatpickerVal["data-disable-date"].value);
                dateData.disable = dates.toString().split(",");
            }
            if (isFlatpickerVal["data-week-number"]) {
                var dates = [];
                dates.push(isFlatpickerVal["data-week-number"].value);
                dateData.weekNumbers = true;
            }
            flatpickr(item, dateData);
        } else if (item.getAttribute("data-provider") == "timepickr") {
            var timeData = {};
            var isTimepickerVal = item.attributes;
            if (isTimepickerVal["data-time-basic"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.dateFormat = "H:i");
            }
            if (isTimepickerVal["data-time-hrs"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.dateFormat = "H:i"),
                    (timeData.time_24hr = true);
            }
            if (isTimepickerVal["data-min-time"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.dateFormat = "H:i"),
                    (timeData.minTime =
                        isTimepickerVal["data-min-time"].value.toString());
            }
            if (isTimepickerVal["data-max-time"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.dateFormat = "H:i"),
                    (timeData.minTime =
                        isTimepickerVal["data-max-time"].value.toString());
            }
            if (isTimepickerVal["data-default-time"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.dateFormat = "H:i"),
                    (timeData.defaultDate =
                        isTimepickerVal[
                            "data-default-time"
                            ].value.toString());
            }
            if (isTimepickerVal["data-time-inline"]) {
                (timeData.enableTime = true),
                    (timeData.noCalendar = true),
                    (timeData.defaultDate =
                        isTimepickerVal[
                            "data-time-inline"
                            ].value.toString());
                timeData.inline = true;
            }
            flatpickr(item, timeData);
        }
    });
</script>
</body>

</html>
