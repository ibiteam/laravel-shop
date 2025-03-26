import { expect, test } from 'vitest'
import { getPrivacyPhone } from '@/utils/public'

test('get privacy phone', () => {
    const phone = '13222222222'
    expect(getPrivacyPhone(phone)).toBe('132****2222')
})
