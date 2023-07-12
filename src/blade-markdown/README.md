# blade markdown

### 简介
* 这是一个对`Blade`模板引擎的扩展，使其支持`Markdown`
* 本组件只做基于[spatie/laravel-markdown](https://spatie.be/docs/laravel-markdown/v1/introduction)的适配改造，在`hyperf`框架下
开箱即用

### 依赖
* `hyperf/view-engine:^3.0`
* `patie/laravel-markdown:^2.3`
* `hyperf/view-engine`配置为blade模板，如
```
// config/autoload/view.php
return [
    'engine' => Hyperf\ViewEngine\HyperfViewEngine::class,
    'mode' => Mode::SYNC,
    'config' => [
        'view_path' => BASE_PATH . '/storage/view/',
        'cache_path' => BASE_PATH . '/runtime/view/',
    ],
];
```

### 安装
安装组件包
```shell
composer require maliboot/blade-markdown
```
发布配置
```shell
php bin/hyperf.php vendor:publish maliboot/blade-markdown
```

### 使用
#### 在任意的模板文件(xxx.blade.php)里编辑 markdown文本 即可

1、`xml标签`代码块方式 - `<x-markdown></x-markdown>`

```
<x-markdown>
# Hello, {!! $name !!}.
This is a [link to our website](https://hyperf.wiki/3.0/#/zh-cn/view-engine)
</x-markdown>
```

2、`blade-directive`代码块方式 - `@markdown...@endmarkdown`

```
@markdown
# Hello, {!! $name !!}.
This is a [link to our website](https://hyperf.wiki/3.0/#/zh-cn/view-engine)
@endmarkdown
```

3、`blade-directive`代码行方式 - `@markdown(...)`

```
@markdown('# Hello, {!! $name !!}.')
@markdown('This is a [link to our website](https://hyperf.wiki/3.0/#/zh-cn/view-engine)')
```

#### 代码高亮等，更多请参考 [spatie/laravel-markdown](https://spatie.be/docs/laravel-markdown/v1/introduction)