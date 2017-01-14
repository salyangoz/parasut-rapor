# Paraşüt düzenli satış raporları aracı
Paket açıklaması... 
## Framework : Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/salyangoz/parasut-rapor.svg?style=flat-square)](https://packagist.org/packages/salyangoz/parasut-rapor)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Nasıl Yüklenir?

#### Step: 1

Paket bir Laravel paketi olduğu için öncelikle bir Laravel kurulumunuzun yapılmış olması gerekiyor. [Laravel nasıl kurulur.]( https://laravel.com/docs/5.3/installation)

#### Step: 2 

Paketi yüklemek için Laravel'in yüklü olduğu root klasörde aşağıdaki komutu çalıştırmanız gerekli

``` bash
$ composer require salyangoz/parasut-rapor
```

#### Step: 3
Bu paket excel exportu farklı bir paket üzerinden çıkardığı için excel paketini de yüklemeniz gerek;

``` bash
$ composer require maatwebsite/excel
```

#### Step: 4

Eklentinin mevcut Laravel'de kullanılabilmesi için Laravel klasörünüzdeki Config/app.php'ye şu değişiklikleri eklemeniz gerekli:

```php
    'providers' => [
        Salyangoz\ParasutRapor\ParasutRaporServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class
    ],
```

#### Step 5: Enviroment ayarlamaları

Paket, proje için kullanılacak Paraşüt bilgilerinizi ve ayarlarınızı Laravel projenizdeki `.env` dosyasından alır. Hangi değişkenlerin tanımlanacağını bu repodaki `.env.example` dosyasından bakabilirsiniz.

##### Opsiyonel:

Eğer projeniz için paketteki sabitleri değiştirmeniz gerekirse (E-mail metni gibi) publish etmeniz gerekir bunu yapmak için aşağıdaki komutu kullanabilirsiniz:

``
php artisan vendor:publish --provider="Salyangoz\ParasutRapor\ParasutRaporServiceProvider"
``

Bu komutu çalıştırmanız ardından paketin config.php dosyası Laravel projenizin config dizinine `parasut-rapor.php` olarak kopyalanacaktır ve mail view dosyası resources/views yoluna kopyalanır, burda yaptığınız değişiklikler paket içindeki config dosyası ile birleşecek ya da overrite olacaktır.

#### Step 6: Laravel task

Sipariş raporlarının belirttiğiniz mail adreslerine iletilmesi için aşağıdaki gibi bir cronjob tanımlamanız gerekli. 

`App\Console\Kernel.php`

```php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \Salyangoz\ParasutRapor\Commands\Report::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         /* Aylık rapor */
           $schedule->command('parasut-rapor:report')->everyMonth();
           
         /* Haftalık */
          #$schedule->command('parasut-rapor:report')->everyWeek();
          
          /* Günlük rapor */
         #$schedule->command('parasut-rapor:report')->everyDay();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
```

Önemli Not: Laravel task zamanlayıcının çalışması için Web sunucunuzda cron'un çalışıyor olması gerekli.

[Laravel task zamanlama nasıl tanımlanır](https://laravel.com/docs/5.3/scheduling)

Laravel task zamanlamanın da çalışır halde olduğundan emin olduktan olduğumuzda artık hazırız demektir!

Aşağıdaki Konfigurasyonları da tamamladığınızda, taskın doğru çalışıp çalışmadığını, komut satırından doğrudan çalıştırarak test edebilirsiniz:

``
$ php artisan parasut-rapor:report
``

## Nasıl Kullanılır?

### Paraşüt API Konfigürasyon Süreçleri
1. destek@parasut.com adresine Paraşüt'e kayıtlı olduğunuz e-posta adresinden API kullanmak istediğinizi ve bunun bilgilerini sizinle paylaşılmasını istediğiniz bir e-posta gönderiniz.
2. Gelen bilgilerden Application Id, Secret ve paraşüte giriş yaptığınızda üst linkte bulunan 6 haneyle başlayan numara bölümünü ve faturalarınız için açacağınız kategori idsini projenizin .env dosyasına girin.

```
PARASUT_CLIENT_ID=
PARASUT_CLIENT_SECRET=
PARASUT_USERNAME=
PARASUT_COMPANY_ID=
PARASUT_PASSWORD=
PARASUT_CATEGORY_ID=
PARASUT_ACCOUNT_ID=
```

### Email Ayarlamaları (.env)

[Laravel Mail](https://laravel.com/docs/5.3/mail) sayfasındaki bilgiler ışığında laravel projenize mail kurulumunu yapınız. Ardından proje için aşağıda belirtilen değerleri giriniz.

````
EMAIL_FROM_EMAIL=Mailde görünecek gönderen email adresi
EMAIL_FROM_NAME=Mailde görünecek gönderen adı
EMAIL_TO_EMAIL=Raporların gönderileceği email adresleri. (,) ile ayrılır
EMAIL_CC_EMAIL=Raporları cc ile gönderileceği email adresleri. (,) ile ayrılır

````
### Planlama aralığı (.env)

Maillerin hangi aralıklardaki siparişleri çekeceğini aşağıdaki ayar ile belirleyebilirsiniz. Buraya 3 farklı değer alınabilir.

monthly,weekly,daily.

Burada belirttiğiniz değer planladığınız cronjob ile aynı olmalı. Örneğin aylık raporlamayı açtıysanız, cronu da aylık olacak şekilde ayarlamalısınız.

````
PARASUT_REPORT_PERIOD=monthly

````

### Dahil edilecek faturalar (.env)

Sistem sadece belirttiğiniz karakterlerle başlayan faturaların raporunu alır. (Eğer boş bırakırsanız tüm faturaları).
````
PARASUT_REPORT_INVOICE_PREFIX=KR,KU
````

## Güvenlik

Herhangi bir güvenlik açığı yakalarsanız, issue açmak yerine info@salyangoz.com.tr adresine bildirim yapabilirsiniz.

## Geliştirilme Platformu

* [Laravel](www.laravel.com) - PHP Framework For Web Artisans

## Versiyonlama

* [SemVer](http://semver.org/) versiyonlamayı kullanıyoruz. Versiyonlamaları görebilmek için [tag](https://github.com/salyangoz/parasut-rapor/tags) bölümünü ziyaret edin.

## Katılımcılar

- [Salyangoz Teknoloji](https://github.com/salyangoz)
- [İbrahim Ş. Örencik](https://github.com/yedincisenol)
- [Ece Bitiren](https://github.com/ecuci)

## Lisans

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Ekstralar

[Salyangoz Web Adresi](https://www.salyangoz.com.tr)
