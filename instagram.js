function detectInstagram(url) {
  url = url.trim();

  // URL 형식 보정 (https:// 없으면 추가)
  if (!/^https?:\/\//i.test(url)) {
    url = 'https://' + url;
  }

  // instagram.com 도메인 확인
  if (!/instagram\.com/i.test(url)) {
    return { valid: false, type: null, message: '인스타그램 주소가 아닙니다.' };
  }

  // 피드
  const feedMatch = url.match(/instagram\.com\/p\/([\w-]+)/);
  if (feedMatch) {
    return { valid: true, type: 'feed', code: feedMatch[1], url };
  }

  // 릴스
  const reelMatch = url.match(/instagram\.com\/reel\/([\w-]+)/);
  if (reelMatch) {
    return { valid: true, type: 'reel', code: reelMatch[1], url };
  }

  // 계정
  const accountMatch = url.match(/instagram\.com\/(?!p\/|reel\/|explore\/|stories\/|accounts\/)([a-zA-Z0-9_.]{1,30})/);
  if (accountMatch) {
    return { valid: true, type: 'account', username: accountMatch[1], url };
  }

  return { valid: false, type: null, message: '올바른 인스타그램 주소가 아닙니다.' };
}

// 사용 예시
const inputs = [
  'instagram.com/p/ABC123/',
  'https://www.instagram.com/reel/XYZ789/',
  'www.instagram.com/sonbh34',
  'https://instagram.com/explore/',
  '아무말',
];

inputs.forEach(input => {
  const result = detectInstagram(input);
  console.log(input, '→', result);
});
