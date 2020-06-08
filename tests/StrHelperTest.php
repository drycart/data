<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\StrHelper;

/**
 * @author mendel
 */
class StrHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testContain()
    {
        $this->assertTrue(StrHelper::contain('qwertyuiop', 'rty'));
        $this->assertFalse(StrHelper::contain('qwertyuiop', '123'));
        //
        $this->assertTrue(StrHelper::contain('qwerTyuiop', 'rty', false));
        $this->assertFalse(StrHelper::contain('qwerTyuiop', 'rty'));
    }
    
    public function testStart()
    {
        $this->assertTrue(StrHelper::start('qwertyuiop', 'qwe'));
        $this->assertFalse(StrHelper::start('qwertyuiop', '123'));
        //
        $this->assertTrue(StrHelper::start('qWertyuiop', 'qwe', false));
        $this->assertFalse(StrHelper::start('qWertyuiop', 'qwe'));
    }
    
    public function testEnd()
    {
        $this->assertTrue(StrHelper::end('qwertyuiop', 'iop'));
        $this->assertFalse(StrHelper::end('qwertyuiop', '123'));
        //
        $this->assertTrue(StrHelper::end('qwertyuioP', 'iop', false));
        $this->assertFalse(StrHelper::end('qwertyuioP', 'iop'));
    }
    
    public function testLike()
    {
        $this->assertTrue(StrHelper::like('qwertyuiop', '%iop'));
        $this->assertFalse(StrHelper::like('qwertyuiop', '%123%'));
        //
        $this->assertTrue(StrHelper::like('qwertyuioP', '%iop', false));
        $this->assertFalse(StrHelper::like('qwertyuioP', '%iop'));
    }
    
    public function testRemovePrefix()
    {
        $this->assertEquals(StrHelper::removePrefix('qwertyuiop', 'qwerty'), 'uiop');
        $this->assertEquals(StrHelper::removePrefix('qwertyuiop', 'uiop'), 'qwertyuiop');
    }
    
    public function testFindPrefix()
    {
        $this->assertEquals(
            StrHelper::findPrefix('qwertyuiop', ['qwerty', 'uiop', '123']),
            ['qwerty', 'uiop']
        );
        $this->assertEquals(
            StrHelper::findPrefix('Xsqwertyuiop', ['qwerty', 'uiop', '123']),
            [null, 'Xsqwertyuiop']
        );
        $this->assertEquals(
            StrHelper::findPrefix('Xsqwertyuiop', ['qwerty', 'uiop', '123'], 'default'),
            ['default', 'Xsqwertyuiop']
        );
    }
    
    public function testStr2int()
    {
        $this->assertEquals(StrHelper::str2int('qwertyuiop'), 2731992073887769);
        $this->assertNull(StrHelper::str2int('qwertyuiopasdfghjklzxcvbnm'));
        $this->assertNull(StrHelper::str2int('qwertyuiop@'));
    }
    
    public function testInt2str()
    {
        $this->assertEquals(StrHelper::int2str(2731992073887769), 'qwertyuiop');
    }
        
    public function testCamelCase2underscore()
    {
        $this->assertEquals(StrHelper::camelCase2underscore('camelCaseKey'), 'camel_case_key');
        $this->assertEquals(StrHelper::camelCase2underscore('CamelCaseKey'), 'camel_case_key');
        $this->assertEquals(StrHelper::camelCase2underscore('underscoupe_string'), 'underscoupe_string');
    }
            
    public function testUnderscore2camelCase()
    {
        $this->assertEquals(StrHelper::underscore2camelCase('underscoupe_string'), 'underscoupeString');
        $this->assertEquals(StrHelper::underscore2camelCase('camelCaseKey'), 'camelCaseKey');
    }
            
    public function testKey2Title()
    {
        $this->assertEquals(StrHelper::key2Label('forum.admins.count()'), 'Forum admins count');
        $this->assertEquals(StrHelper::key2Label('forum_admins.Count()'), 'Forum admins count');
        $this->assertEquals(StrHelper::key2Label('ForumAdminsCount'), 'Forum admins count');
        $this->assertEquals(StrHelper::key2Label('Some_strangeDataKey'), 'Some strange data key');
    }
    
    public function testParseDocComment()
    {
        $doc = implode("\n", [
            '/**',
            ' * Some text',
            ' * @return int',
            '*/'
        ]);
        $this->assertEquals(StrHelper::parseDocComment($doc), ['Some text','@return int']);
        $this->assertEquals(StrHelper::parseDocComment('* qwerty'), []);
    }
    
    public function testToString()
    {
        $this->assertEquals(
            StrHelper::templateToString('/article/{type}/{slug}',['type'=>'news','slug'=>'test-template']),
            '/article/news/test-template'
        );
        $this->assertEquals(
            StrHelper::templateToString('/article/{article.type}/{article.slug}',['article'=>['type'=>'news','slug'=>'test-template']]),
            '/article/news/test-template'
        );
        $this->assertEquals(
            StrHelper::templateToString('/article/{article.type}/{article.slug}',(object)['article'=>['type'=>'news','slug'=>'test-template']]),
            '/article/news/test-template'
        );
    }
    public function testFromString()
    {
        $this->assertEquals(
            StrHelper::templateFromString('/article/{type}/{slug}','/article/news/test-template'),
            ['type'=>'news','slug'=>'test-template']
        );
        $this->assertNull(
            StrHelper::templateFromString('/article/{type}/{slug}','/admin/news/test-template')
        );
    }
    
}
