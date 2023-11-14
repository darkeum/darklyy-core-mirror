@extends('layouts.base')

@section('title', 'Tree')

@section('vendor-style')
  <!-- vendor css files -->
  {{--<link rel="stylesheet" href="{{ asset(mix('fonts/font-awesome/css/font-awesome.min.css'))}}">--}}
  {{--<link rel="stylesheet" href="{{ asset('fonts/font-awesome/css/font-awesome.min.css') }}">--}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jstree.min.css'))}}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css')) }}">
@endsection

@section('content')
<!-- Tree section -->
<section class="basic-custom-icons-tree">
  <div class="row">

    <!-- Custom Icons Tree -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Custom Icons</h4>
        </div>
        <div class="card-body">
          <div id="jstree-custom-icons"></div>
        </div>
      </div>
    </div>
    <!--/ Custom Icons Tree -->
  </div>
</section>

<!--/ Tree section -->
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/extensions/jstree.min.js')) }}"></script>
@endsection

@section('page-script')
  <!-- Page js files -->
  {{--<script src="{{ asset(mix('js/scripts/extensions/ext-component-tree.js')) }}"></script>--}}

  <script>
    $(function () {
      'use strict';

      var customIconsTree = $('#jstree-custom-icons');

      // var assetPath = '../../../app-assets/';
      // if ($('body').attr('data-framework') === 'laravel') {
      //   assetPath = $('body').attr('data-asset-path');
      // }

      // Custom Icons
      if (customIconsTree.length) {
        customIconsTree.jstree({
          core: {
            data: [
              {
                text: 'Документы',
                state: {
                  opened: true
                },
                children: [
                  {
                    text: 'Соглашения',
                    children: [
                      {
                        text: 'Соглашение №1',
                        type: 'html',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №2',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'pdf'
                          },
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'docx'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №3',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                    ]
                  },
                  {
                    text: 'Договора',
                    children: [
                      {
                        text: 'Соглашение №1',
                        type: 'html',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №2',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'pdf'
                          },
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'docx'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №3',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                    ]
                  },
                  {
                    text: 'Что то еще',
                    children: [
                      {
                        text: 'Соглашение №1',
                        type: 'html',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №2',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'pdf'
                          },
                          {
                            text: '<span class="badge badge-sm bg-info">Добавить</span>',
                            type: 'docx'
                          }
                        ]
                      },
                      {
                        text: 'Соглашение №3',
                        state: {
                          opened: true
                        },
                        children: [
                          {
                            text: 'bg.jpg',
                            type: 'img'
                          },
                          {
                            text: 'logo.png',
                            type: 'img'
                          },
                          {
                            text: 'avatar.png',
                            type: 'img'
                          }
                        ]
                      },
                    ]
                  },
                ]
              }
            ]
          },
          plugins: ['types'],
          types: {
            default: {
              icon: 'far fa-folder'
            },
            pdf: {
              icon: 'fas fa-file-pdf text-danger'
            },
            docx: {
              icon: 'fas fa-file-word text-info'
            },
            html: {
              icon: 'fab fa-html5 text-danger'
            },
            css: {
              icon: 'fab fa-css3-alt text-info'
            },
            img: {
              icon: 'far fa-file-image text-success'
            },
            js: {
              icon: 'fab fa-node-js text-warning'
            }
          }
        });
      }
    });
  </script>

@endsection
