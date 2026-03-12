# PHP Login App - 프로젝트 현황

## 프로젝트 정보
- **저장소**: https://github.com/sonbh34/php-login-app
- **배포 URL**: https://web-production-ca576.up.railway.app/
- **로컬 경로**: D:/작업폴더/ClaudeCode/login
- **Git 사용자**: sonbh34 / cata@imgsrc.co.kr

## 기술 스택
- PHP 8.2
- MySQL
- Railway (호스팅)
- GitHub Codespaces (개발환경)

## 파일 구조
- `index.php` — 로그인 페이지
- `login.php` — 로그인 처리
- `register.php` — 회원가입
- `logout.php` — 로그아웃
- `dashboard.php` — 로그인 후 대시보드
- `forgot_password.php` — 비밀번호 찾기
- `reset_password.php` — 비밀번호 재설정
- `config.php` — DB 연결 (Railway 환경변수 사용)
- `init_db.php` — DB 테이블 초기화 (최초 1회 실행)
- `style.css` — 스타일
- `Procfile` — Railway 실행 명령어
- `.devcontainer/` — Codespaces 설정

## DB 구조 (users 테이블)
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(64) DEFAULT NULL,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 현재 문제
- Railway에 MySQL 서비스 미연결 상태
- `init_db.php` 접속 시 500 오류 발생
- 회원가입 작동 안 됨

## 해결 방법 (미완료)
1. Railway 대시보드 → **+ New** → **Database** → **Add MySQL**
2. PHP 앱 서비스 → **Variables** 탭에서 아래 환경변수 확인:
   - `MYSQLHOST`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   - `MYSQLDATABASE`
   - `MYSQLPORT`
3. 환경변수 연결 후 `https://web-production-ca576.up.railway.app/init_db.php` 접속 → DB 초기화

## Git 작업 흐름
```bash
git add .
git commit -m "변경 내용"
git push
```
→ Railway에 자동 배포됨
