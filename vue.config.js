const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  // vue.config.js

  chainWebpack: config => {
    config.module
        .rule('glb')
        .test(/\.glb$/)
        .use('file-loader')
        .loader('file-loader')
        .options({
          name: 'assets/models/[name].[hash:8].[ext]'
        });
      config.module
          .rule('gltf')
          .test(/\.gltf$/)
          .use('file-loader')
          .loader('file-loader')
          .options({
              name: 'assets/models/[name].[hash:8].[ext]'
          });
      config.module
          .rule('images')
          .test(/\.(png|jpe?g|gif|svg)(\?.*)?$/)
          .use('file-loader')
          .loader('file-loader')
          .options({
              name: 'assets/images/[name].[hash:8].[ext]'
          });
  },  // process glb file
  publicPath: process.env.NODE_ENV === 'production'
      ? '/icegiants/frontend/' // for pruduction environment
      : '/' // for exploitation environment
})