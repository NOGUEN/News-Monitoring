# News-Monitoring

## 목차
[1. 프로그램 설명](#프로그램-설명)  
[2. REST API 명세서](#REST-API-명세서)  
[3. 테스트 가이드](#테스트-가이드)  


## 프로그램 설명
### 사용된 기술
- php 8.3
- composer
- aws ec2 linux2

### 파일 구조
클래스와 관련된 폴더와 파일은 `UpperCamelCase`로, 그 외의 폴더와 파일들은 `snake_case`로 작성했습니다.

  > 프로그래밍을 할 때 클래스와 클래스가 아닌 것의 구분이 명확했으면 좋겠다고 생각이 들어 이 방식을 채택했습니다.

```
news-monitoring-site/  
├── public/  
│   ├── index.php  
├── script/  
│   ├── news_fetcher.php  
├── src/  
│   ├── Controllers/  
│       ├── NewsController.php  
│   ├── Models/  
│       ├── News.php  
│       ├── NewsSite.php  
│   ├── Views/  
│       ├── NewsController.php  
│   ├── config.php  
│   ├── routes.php  
├── vendor/  
├── composer.json  
├── .env  
```


<br>

### 클래스 다이어그램 + DB 스키마
![Frame 1](https://github.com/NOGUEN/News-Monitoring/assets/65299607/0fe72226-c913-45ec-99a2-5d33d18d5beb)


#### news 테이블
- `id`: **int**, 기본 키 (Primary Key)
- `site_id`: **int**, 외래 키 (Foreign Key) (`news_sites` 테이블의 `id`를 참조)
- `title`: **varchar**, 뉴스 제목
- `link`: **text**, 뉴스 링크
- `published_at`: **timestamp**, 뉴스 발행 시간

<br>

#### news_sites 테이블
- `id`: **int**, 기본 키 (Primary Key)
- `name`: **varchar**, 뉴스 사이트 이름
- `url`: **varchar**, 뉴스 사이트 RSS 피드 URL
- `created_at`: **timestamp**, 생성 시간

<br>

### 스크립트
[스크립트 코드 링크](https://github.com/NOGUEN/News-Monitoring/blob/main/scripts/news_fetcher.php)

매 5분(설정에 따라 더 짧게도 가능)마다 `news_sites` 데이터 베이스에 있는 뉴스 사들의 데이터를 가져옵니다.
중복이 되지 않게 `link`가 같은지 확인 한 후, 만약에 `link`가 같다면 넣지 않습니다.
`news_sites`에 새로운 뉴스사를 넣으면 이에 맞춰 `news`가 매 5분마다 갱신됩니다.

<br>

#### 파싱
일반적으로 대부분의 rss에서는 같은 형식을 취하고 있습니다.
`title`, `link`, `description`, `published_at` 등등... 같은 이름으로 데이터를 반환해줍니다.
하지만 `published_at`의 경우에는 뉴스사 마다 형식이 다르기에 데이터를 바로 저장할 수 없습니다.
String으로 바꿔서 저장하면 큰 문제가 되는 부분은 아니지만, 날짜 형식을 한 형식으로 통일성 있게 저장하고 싶기에 `parseDate`를 넣었습니다.
```php
function parseDate($dateString) {
    $formats = [
        'D, d M Y H:i:s T',
        'D, j M Y H:i:s O',
        'D, d M Y H:i:s P',
        'Y.m.d',
    ];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }
    }

    return null;
}
```


<br>

### 플로우
<img width="2496" alt="Untitled" src="https://github.com/NOGUEN/News-Monitoring/assets/65299607/38ba210a-c68b-4f24-ad8c-215e25ef2729">

<br>

## REST API 명세서
**배포된 서버 URL** : http://ec2-43-200-192-75.ap-northeast-2.compute.amazonaws.com

### 1. **뉴스 사이트 모두 가져오기**
- **URI**: `/`
- **HTTP Method**: GET
- **Description**: 모든 뉴스 사이트를 반환합니다.
- **Request Parameters**: 없음
- **Response Example**:
  ```json
  {
      "news_sites": [
          {
              "id": 1,
              "name": "News Site 1",
              "url": "http://newssite1.com/rss"
          },
      ]
  }
  ```
  
<br>

### 2. **Site Id를 통해 특정 뉴스 가져오기**
- **URI**: `/news/{siteId}`
- **HTTP Method**: GET
- **Description**: 특정 뉴스 사이트의 모든 뉴스를 반환합니다.
- **Request Parameters**:
  - `siteId` (path parameter): 뉴스 사이트의 ID
- **Response Example**:
  ```json
  {
      "news": [
          {
              "id": 1,
              "site_id": 1,
              "title": "News Title",
              "link": "http://newssite1.com/news1",
              "published_at": "2024-01-01 00:00:00"
          },
      ]
  }
  ```
  
<br>

### 3. **모든 뉴스 가져오기**
- **URI**: `/news`
- **HTTP Method**: GET
- **Description**: 모든 뉴스를 반환합니다.
- **Request Parameters**: 없음
- **Response Example**:
  ```json
  {
      "news": [
          {
              "id": 1,
              "site_id": 1,
              "title": "News Title",
              "link": "http://newssite1.com/news1",
              "published_at": "2024-01-01 00:00:00"
          },
      ]
  }
  ```
  
<br>

### 4. **페이징 된 전체 뉴스 가져오기**
- **URI**: `/news/page/default`
- **HTTP Method**: GET
- **Description**: 페이지 단위로 모든 뉴스를 반환합니다.
- **Request Parameters**:
  - `limit` (query parameter, optional): 한 페이지에 표시할 뉴스 수 (기본값: 10)
  - `page` (query parameter, optional): 페이지 번호 (기본값: 1)
- **Response Example**:
  ```json
  {
      "news": [
          {
              "id": 1,
              "site_id": 1,
              "title": "News Title",
              "link": "http://newssite1.com/news1",
              "published_at": "2024-01-01 00:00:00"
          },
      ]
  }
  ```
  
<br>

### 5. **페이징 된 특정 뉴스사의 뉴스 가져오기**
- **URI**: `/news/page/site`
- **HTTP Method**: GET
- **Description**: 특정 뉴스 사이트의 뉴스를 페이지 단위로 반환합니다.
- **Request Parameters**:
  - `limit` (query parameter, optional): 한 페이지에 표시할 뉴스 수 (기본값: 10)
  - `page` (query parameter, optional): 페이지 번호 (기본값: 1)
  - `siteId` (query parameter, optional): 뉴스 사이트의 ID (기본값: 1)
- **Response Example**:
  ```json
  {
      "news": [
          {
              "id": 1,
              "site_id": 1,
              "title": "News Title",
              "link": "http://newssite1.com/news1",
              "published_at": "2024-01-01 00:00:00"
          },
      ]
  }
  ```
  
<br>

### 6. **전체 뉴스의 페이지 수 가져오기**
- **URI**: `/news/page/count`
- **HTTP Method**: GET
- **Description**: 뉴스의 전체 페이지 수를 반환합니다.
- **Request Parameters**:
  - `limit` (query parameter, optional): 한 페이지에 표시할 뉴스 수 (기본값: 10)
- **Response Example**:
  ```json
  {
      "total_pages": 5
  }
  ```
  
<br>

### 7. **특정 뉴스 사의 뉴스의 페이지 수 가져오기**
- **URI**: `/news/page/count/site`
- **HTTP Method**: GET
- **Description**: 특정 뉴스 사이트의 뉴스의 전체 페이지 수를 반환합니다.
- **Request Parameters**:
  - `limit` (query parameter, optional): 한 페이지에 표시할 뉴스 수 (기본값: 10)
  - `siteId` (query parameter, optional): 뉴스 사이트의 ID (기본값: 1)
- **Response Example**:
  ```json
  {
      "total_pages": 5
  }
  ```

<br>

## 테스트 가이드
### 링크 직접 사용
**배포된 서버 URL** : http://ec2-43-200-192-75.ap-northeast-2.compute.amazonaws.com
```
http://ec2-43-200-192-75.ap-northeast-2.compute.amazonaws.com/Rest/Api/Path
```

### 프론트 엔드(Flutter)로 테스트
#### 배포된 사이트(이슈 발생으로 실행 안됨)
[News-Monitoring-Flutter](https://github.com/NOGUEN/news-monitoring-flutter)

[배포된 주소](https://news-monitoring-37d53.web.app)
플러터로 웹 빌드를 한 후, firebase hosting으로 배포를 해놓은 상황이나 ssl 인증을 받지 못해 서버에서 http로 제공을 하게 되면서 api가 실행이 되지 않는 상황입니다.
firebase hosting은 기본적으로 https로 들어오는 요청만 허용을 하고, 인증되지 않은 http요청은 허용하지 않기에 이런 문제가 발생하고 있습니다.

#### 로컬에서 직접 프론트엔드 실행
flutter가 설치되어있어야 합니다...!
이 부분 제대로 구현하지 못해 죄송합니다.
[플러터 설치 방법](https://codingapple.com/unit/flutter-install-on-windows-and-mac/)

```
git clone git@github.com:NOGUEN/news-monitoring-flutter.git
// 클론 받은 디렉토리로 이동
flutter pub get
flutter run
// run 후 크롬 선택
```

### 시연 영상
![GIFMaker_me (2)](https://github.com/NOGUEN/News-Monitoring/assets/65299607/c6f0b014-8970-40cc-8965-dff507e9ce10)


