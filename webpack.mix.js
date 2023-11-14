const mix = require("laravel-mix");
const exec = require("child_process").exec;
require("dotenv").config();

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const glob = require("glob");
const path = require("path");

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
    (glob.sync("resources/" + query) || []).forEach((f) => {
        f = f.replace(/[\\\/]+/g, "/");
        cb(f, f.replace("resources", "public"));
    });
}

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsModulesDir(query, cb) {
    (glob.sync("usr/modules/**/Resources/Assets/" + query) || []).forEach((f) => {
        f = f.replace(/[\\\/]+/g, "/");
        cb(f, f.replace("resources", "public"));
    });
}

const sassOptions = {
    precision: 5,
    includePaths: ["node_modules", "resources/assets/"],
};

// Основные таблицы стилей плагинов - plugins Core stylesheets
mixAssetsDir("scss/base/plugins/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest.replace(/(\\|\/)scss(\\|\/)/, "$1css$2").replace(/\.scss$/, ".css"), { sassOptions }
    )
);

// Основные таблицы стилей страниц - pages Core stylesheets
mixAssetsDir("scss/base/pages/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest.replace(/(\\|\/)scss(\\|\/)/, "$1css$2").replace(/\.scss$/, ".css"), { sassOptions }
    )
);

// Основные таблицы стилей - Core stylesheets
mixAssetsDir("scss/base/core/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest.replace(/(\\|\/)scss(\\|\/)/, "$1css$2").replace(/\.scss$/, ".css"), { sassOptions }
    )
);

// Скрипты js - script js
mixAssetsDir("js/scripts/**/*.js", (src, dest) => mix.scripts(src, dest));

/*
 |--------------------------------------------------------------------------
 | Ресурсы приложения - Application assets
 |--------------------------------------------------------------------------
 */

mixAssetsDir("vendors/js/**/*.js", (src, dest) => mix.scripts(src, dest));
mixAssetsDir("vendors/css/**/*.css", (src, dest) => mix.copy(src, dest));
mixAssetsDir("vendors/**/**/images", (src, dest) => mix.copy(src, dest));
mixAssetsDir("vendors/css/editors/quill/fonts/", (src, dest) =>
    mix.copy(src, dest)
);
mixAssetsDir("fonts", (src, dest) => mix.copy(src, dest));
mixAssetsDir("fonts/**/**/*.css", (src, dest) => mix.copy(src, dest));
mix.copyDirectory("resources/images", "public/images");
mix.copyDirectory("resources/data", "public/data");
mix.copyDirectory("resources/js/grid", "public/js/grid");

mix
    .js("resources/js/core/app-menu.js", "public/js/core")
    .js("resources/js/core/app.js", "public/js/core")
    .js("resources/assets/js/scripts.js", "public/js/core")
    .sass(
        "resources/scss/base/themes/dark-layout.scss",
        "public/css/base/themes", { sassOptions }
    )
    .sass(
        "resources/scss/base/themes/bordered-layout.scss",
        "public/css/base/themes", { sassOptions }
    )
    .sass(
        "resources/scss/base/themes/semi-dark-layout.scss",
        "public/css/base/themes", { sassOptions }
    )
    .sass("resources/scss/core.scss", "public/css", { sassOptions })
    .sass("resources/scss/overrides.scss", "public/css", { sassOptions })
    .sass("resources/scss/base/custom-rtl.scss", "public/css-rtl", {
        sassOptions,
    })
    .sass("resources/assets/scss/style-rtl.scss", "public/css-rtl", {
        sassOptions,
    })
    .sass("resources/assets/scss/style.scss", "public/css", { sassOptions });

mix.then(() => {
    if (process.env.MIX_CONTENT_DIRECTION === "rtl") {
        let command = `node ${path.resolve(
      "node_modules/rtlcss/bin/rtlcss.js"
    )} -d -e ".css" ./public/css/ ./public/css/`;
        exec(command, function(err, stdout, stderr) {
            if (err !== null) {
                console.log(err);
            }
        });
    }
});

function getEntry() {
    let entry = {};
    glob
        .sync(__dirname + "/usr/modules/*/Resources/Assets/js/*.js")
        .forEach(function(file) {
            let module = file.match(/modules\/([^/]+?)\/Resources/)[1];
            let name = file.match(/([^/]+?)\.js/)[1];
            mix.js(file, "public/js/modules/" + module + "/" + name + ".js");
        });

    glob
        .sync(__dirname + "/usr/modules/*/Resources/Assets/css/*.css")
        .forEach(function(file) {
            let module = file.match(/modules\/([^/]+?)\/Resources/)[1];
            let name = file.match(/([^/]+?)\.css/)[1];

            mix.copy(file, "public/css/modules/" + module + "/" + name + ".css");
        });
}

getEntry();

// if (mix.inProduction()) {
//   mix.version()
//   mix.webpackConfig({
//     output: {
//       publicPath: '/demo/vuexy-bootstrap-laravel-admin-template-new/demo-2/'
//     }
//   })
//   mix.setResourceRoot('/demo/vuexy-bootstrap-laravel-admin-template-new/demo-2/')
// }