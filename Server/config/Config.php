<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 17:17
 */

namespace config;


class Config
{
    const SURVEY_TABLE_NAME = 'serveyForm';
    const STUDENT_TABLE_NAME = 'student';
    const TEACHER_TABLE_NAME = 'teacher';
    const ADMIN_TABLE_NAME = 'admin';
    const CLASS_TABLE_NAME = 'class';
    const CRITERIA_TABLE_NAME = 'criteriaLevel';
    const USER_TABLE_NAME = 'user';
    const DEFAULT_TOKEN = "";
    const DEFAULT_PASSWORD = "12345678";
    const DEFAULT_FORM = [['Giảng đường đáp ứng yêu cầu môn học'],
                            ['Thiết bị giảng đường đáp ứng yêu cầu giảng dạy và học tập'],
                            ['Bạn được hỗ trợ kịp thời trong môn học này'],
                            ['Mục tiêu môn học nêu rõ kiến thức và kỹ năng mà người học cần đạt được'],
                            ['Thời lượng môn học phân bố hợp lý cho các hình thức học tập'],
                            ['Các tài liệu môn học được cập nhật'],
                            ['Môn học góp phần cung cấp kiến thức kỹ năng nghề nghiệp cho bạn'],
                            ['Giảng viên thực hiện đầy đủ nội dung và thời lượng của môn học theo kế hoạch'],
                            ['Giảng viên hướng dẫn bạn phương pháp học tập khi bắt đầu môn học'],
                            ['Phương pháp giảng dạy của giảng viên giúp bạn phát triển tư duy'],
                            ['Giảng viện tạo cơ hội cho bạn chủ động tham gia vào các hoạt động'],
                            ['Giảng viên giúp bạn phát triển kỹ năng làm việc độc lập'],
                            ['Giảng viên rèn luyện cho bạn phương pháp liên hệ các vấn đề trong môn học và các vấn đề thực tiễn'],
                            ['Giảng viên sử dụng hiệu quả phương tiện dạy học'],
                            ['Giảng viên quan tâm rèn luyện tư cách phẩm chất nghề nghiệp của người học'],
                            ['Bạn hiểu những vấn đề được truyền đạt trên lớp']];

    const CONFIG_SERVER = array(
        'jwt' => array(
            'key'       => 'BXYCmeu0r7vqkNzVfWFJR+l4ljSTa9JXH7qAYywVhuU9WwDxKNyeclZ2LiFr9am0cgn52JHSeo2Niqq2iq5rSQ==',     // Key for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
            'algorithm' => 'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        ),
        'database' => array(
            'user'     => 'root', // Database username
            'password' => 'root', // Database password
            'host'     => 'bulletin.any.com.vn', // Database host
            'name'     => 'servey', // Database schema name
        ),
        'serverName' => 'bulletin.any.com.vn/',
    );
    const LENGTH_OPENSSL_GENERATE = 64;
}