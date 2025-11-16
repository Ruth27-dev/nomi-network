// Prevent Node-style require usage if needed
window.require = undefined;

// Core utilities
import $ from "jquery";
window.$ = window.jQuery = $;

import _ from "lodash";
window._ = _;

import Axios from "axios";
window.Axios = Axios;

// Feather icons
import feather from "feather-icons";
window.feather = feather;

// Moment.js
import moment from "moment";
window.moment = moment;

// Date Range Picker
import "daterangepicker";
import "daterangepicker/daterangepicker.css";

// Fancybox
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
window.Fancybox = Fancybox;

// Alpine.js and Plugins
import Alpine from "alpinejs";
import plugin from "./alpine_plugin/plugin";
import dialog from "alpinjs-dialog";
import dropdown from "alpinjs-dropdown";

Alpine.plugin(dialog);
Alpine.plugin(dropdown);
Alpine.plugin(plugin);

window.Alpine = Alpine;

// DOM-ready tasks
document.addEventListener("DOMContentLoaded", () => {
    Alpine.start();
    feather.replace(); // Feather icons
});

// UI Components
import FormGroup from "./form-group";
window.FormGroup = FormGroup;

import Table from "./table";
window.Table = Table;

// File Upload
import onUploadFile from "./package/upload-file";
window.$onUploadFile = onUploadFile;

// Validation
import Validator from "./package/validator";
window.$validatorOption = Validator;

// Common utilities
import "./package/common";

// Notifications
import toastr from "toastr";
import "toastr/build/toastr.min.css";
window.toastr = toastr;

// PDF Export
import html2pdf from "html2pdf.js";
window.html2pdf = html2pdf;

// Charts
import Chart from "chart.js/auto";
window.Chart = Chart;

// Print
import printJS from "print-js";
import "print-js/dist/print.css";
window.printJS = printJS;

// Animations
import anime from "./libs/anime.es.js";
window.anime = anime;

// excel js
import ExcelJS from "exceljs";
window.ExcelJS = ExcelJS;
import { saveAs } from "file-saver";
window.saveAs = saveAs;

import mapboxgl from 'mapbox-gl';
window.mapboxgl = mapboxgl;

import firebase from "firebase/compat/app";
