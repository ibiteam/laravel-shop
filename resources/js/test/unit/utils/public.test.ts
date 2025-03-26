import { expect, test } from 'vitest'
import { getPrivacyPhone,isEmail } from '@/utils/public'

test('get privacy phone', () => {
    const phone = '13222222222'
    expect(getPrivacyPhone(phone)).toBe('132****2222')
})

test('is email', () => {
    const email = '998820@qq.com'
    expect(isEmail(email)).toBe(true)
    const email2 = '1@qq.com'
    expect(isEmail(email2)).toBe(false)
})
