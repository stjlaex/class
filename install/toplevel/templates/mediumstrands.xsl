<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a single table with with one to several 
	 assessment grades and a short written comment; the table may span multiple pages -->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
<div id='logo'>
<img src="../images/schoollogo.png" width="130px"/>
</div>

<div class='studenthead'>
	<table>
	  <tr>
		<td colspan="2">
		  <label>
			Student
		  </label>
		  <xsl:value-of select="forename/value/text()" />&#160;
		  <xsl:value-of select="surname/value/text()" />&#160;
		  <xsl:value-of select="middlenames/value/text()" />
		</td>
	  </tr>
	  <tr>		
		<td>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />
		  <xsl:text>&#160;&#160;&#160;&#160;</xsl:text>
		</td>
		<td>
		  <label>
			Date
		  </label>
		<xsl:value-of select="reports/publishdate/text()" />
		</td>
	  </tr>
	</table>
</div>

<div class='spacer'></div>
<xsl:apply-templates select="reports"/>
<div class='spacer'></div>

<hr />

</xsl:template>

<xsl:template match="reports">
  <table class="grades">
	<tr class="label">
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	  <th>Subject teacher's comment.</th>
	</tr>

	<xsl:apply-templates select="report">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>

  </table>

<div class='spacer'></div>

<xsl:apply-templates select="summaries/summary" />

</xsl:template>

<xsl:template match="reports/summaries/summary">
  <xsl:if test="description/type='com' and comments/text()!=' '" >
	<div class="sumcomment">
	  <xsl:value-of select="description/value/text()" /><br />
	  <xsl:apply-templates select="comments"/>
	</div>
  </xsl:if>
</xsl:template>

<xsl:template match="report">
<tr>
  <th>
	<xsl:choose>
	  <xsl:when test="component/value/text()!=' '">
		<xsl:value-of select="component/value/text()" />
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:value-of select="subject/value/text()" />
	  </xsl:otherwise>
	</xsl:choose>
  </th>
  <td colspan="2">
  </td>
</tr>
<tr>
	<xsl:choose>
	  <xsl:when test="component/id/text()!=assessments/assessment/subjectcomponent/value/text()">
		<td colspan="2">
		  <xsl:variable name='strandlabel' select='assessments/assessment/component/value' />
		  <xsl:call-template name="strandrow">
			<xsl:with-param name="strandlabel" select="$strandlabel"/>
		  </xsl:call-template>
		</td>
	  </xsl:when>
	  <xsl:otherwise>
		<td>
		</td>
		<xsl:call-template name="asscell" />

	  </xsl:otherwise>
	</xsl:choose>

		<td class="subject-comment">
		  <xsl:apply-templates select="comments"/>
		  <xsl:text>&#160;</xsl:text>
		</td>

</tr>
</xsl:template>

<xsl:template match="comments">
  <xsl:apply-templates select="comment"/>
</xsl:template>

<xsl:template match="comment">
  <p class="comment-text">
	<xsl:value-of select="text/value/text()" />
	<xsl:text>&#160;(</xsl:text>
	<xsl:value-of select="teacher/value/text()" />
	<xsl:text>)&#160;</xsl:text>
  </p>
</xsl:template>


<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		<xsl:value-of select="$asslabel[$index]" />  
		<xsl:text>&#160; </xsl:text>
	</th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(../asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $maxindex">
	<td>
	  <div>
	  <xsl:value-of select="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	  </div>
	</td>
    <xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template name="strandrow">
  <xsl:param name="strandlabel"> </xsl:param>
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count($strandlabel)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
		  <table class="strand">
			<tr>
			<th>
			  <xsl:value-of select="$strandlabel[$index]" />
			</th>
			<xsl:call-template name="strandasscell">
			  <xsl:with-param name="strand" select="$strandlabel[$index]"/>
			</xsl:call-template>
		  </tr>
		  </table>
		  <xsl:call-template name="strandrow">
			<xsl:with-param name="index" select="$index + 1"/>
			<xsl:with-param name="strandlabel" select="$strandlabel"/>
		  </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template name="strandasscell">
  <xsl:param name="strand"> </xsl:param>
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(../asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $maxindex">
	<td>
	  <div>
	  <xsl:value-of select="assessments/assessment[component/value=$strand][printlabel/value=$asslabel[$index]]/result/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	  </div>
	</td>
    <xsl:call-template name="strandasscell">
	  <xsl:with-param name="strand" select="$strand"/>
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


</xsl:stylesheet>
